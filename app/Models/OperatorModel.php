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
        $nama = trim($nama);
        $username = $this->username($username) ?? throw new DomainException('Username memakai 1–64 huruf, angka, titik, garis bawah atau tanda hubung.');
        if ($nama === '' || mb_strlen($nama) > 255) {
            throw new DomainException('Masukkan nama lengkap maksimal 255 karakter.');
        }
        if (mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
            throw new DomainException('Kata sandi harus memiliki minimal 8 karakter dan maksimal 72 byte.');
        }
        if ($this->where('username', $username)->first() !== null) {
            throw new DomainException('Username sudah dipakai.');
        }

        if ($this->insert(['nama_lengkap' => $nama, 'username' => $username, 'password_hash' => password_hash($password, PASSWORD_DEFAULT)]) === false) {
            throw new DomainException('Akun operator gagal disimpan.');
        }
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

        return ['id' => $operator['id'], 'nama_lengkap' => $operator['nama_lengkap']];
    }

    private function username(string $username): ?string
    {
        $username = strtolower(trim($username));

        return preg_match('/^[a-z0-9._-]{1,64}$/D', $username) === 1 ? $username : null;
    }
}
