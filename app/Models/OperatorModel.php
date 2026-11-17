<?php

namespace App\Models;

use CodeIgniter\Model;
use DomainException;

final class OperatorModel extends Model
{
    protected $table = 'operator';

    protected $allowedFields = ['nama_lengkap', 'username', 'password_hash'];

    private const DUMMY_HASH = '$2y$12$38fD6dIfQ0xZCJM5IHNuZOkckDAX6aPMy3tTi5uJZNQYv7RfhXh6.';

    public function buatAkun(string $nama, string $username, #[\SensitiveParameter] string $password): void
    {
        if ($this->insert($this->dataAkun($nama, $username, $password)) === false) {
            throw new DomainException('Akun operator gagal disimpan.');
        }
    }

    public function ubahAkun(int $id, string $nama, string $username, #[\SensitiveParameter] string $password): void
    {
        $data = $this->dataAkun($nama, $username, $password === '' ? null : $password, $id);
        if ($this->update($id, $data) === false) {
            throw new DomainException('Akun operator gagal disimpan.');
        }
    }

    public function hapusAkun(int $id, int $idOperator): void
    {
        if ($id === $idOperator) {
            throw new DomainException('Akun yang sedang digunakan tidak bisa dihapus.');
        }

        if (! $this->db->transBegin()) {
            throw new DomainException('Penghapusan akun gagal dimulai.');
        }

        try {
            // Lock both accounts so concurrent requests cannot delete each other's accounts.
            $tabel = $this->db->protectIdentifiers($this->table, true);
            $akun = $this->db->query("SELECT id FROM {$tabel} WHERE id IN (?, ?) ORDER BY id FOR UPDATE", [$id, $idOperator]);
            if ($akun === false || $akun->getNumRows() !== 2 || ! $this->delete($id)
                || ! $this->db->transStatus() || ! $this->db->transCommit()) {
                throw new DomainException('Akun tidak dapat dihapus. Muat ulang daftar operator.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function profilSesi(array $operator): array
    {
        return [
            'operator_id' => $operator['id'],
            'operator_nama' => $operator['nama_lengkap'],
            'operator_kunci' => hash('sha256', $operator['password_hash']),
        ];
    }

    public function autentikasi(string $username, #[\SensitiveParameter] string $password): ?array
    {
        $username = $this->username($username);
        $operator = $username === null ? null : $this->where('username', $username)->first();
        if (strlen($password) > 72 || str_contains($password, "\0")) {
            return null;
        }
        $cocok = password_verify($password, $operator['password_hash'] ?? self::DUMMY_HASH);
        if (! $cocok || $operator === null) {
            return null;
        }

        return $this->profilSesi($operator);
    }

    private function dataAkun(string $nama, string $username, #[\SensitiveParameter] ?string $password, ?int $id = null): array
    {
        $nama = trim($nama);
        $username = $this->username($username) ?? throw new DomainException('Username memakai 1–64 huruf, angka, titik, garis bawah atau tanda hubung.');
        if ($nama === '' || mb_strlen($nama) > 255) {
            throw new DomainException('Masukkan nama lengkap maksimal 255 karakter.');
        }
        if ($password !== null && (mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0"))) {
            throw new DomainException('Kata sandi harus memiliki minimal 8 karakter dan maksimal 72 byte.');
        }
        $akun = $this->where('username', $username)->first();
        if ($akun !== null && (int) $akun['id'] !== $id) {
            throw new DomainException('Username sudah dipakai.');
        }

        $data = ['nama_lengkap' => $nama, 'username' => $username];
        if ($password !== null) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $data;
    }

    private function username(string $username): ?string
    {
        $username = strtolower(trim($username));

        return preg_match('/^[a-z0-9._-]{1,64}$/D', $username) === 1 ? $username : null;
    }
}
