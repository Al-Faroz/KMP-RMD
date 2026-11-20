<?php

namespace App\Models;

use CodeIgniter\Model;
use DomainException;
use RuntimeException;

final class AksesCetakModel extends Model
{
    private const SETTING_ID = 1;

    protected $table = 'akses_cetak';

    public function pengaturan(): ?array
    {
        return $this->find(self::SETTING_ID);
    }

    public function atur(string $nomor, string $kode): void
    {
        $nomor = preg_replace('/[\s().-]+/', '', trim($nomor));
        if (str_starts_with($nomor, '+')) {
            $nomor = substr($nomor, 1);
        } elseif (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }
        if (! preg_match('/^[1-9][0-9]{7,14}$/D', $nomor)) {
            throw new DomainException('Masukkan nomor WhatsApp yang valid, misalnya 08… atau +62….');
        }

        $builder = $this->builder();
        if ($kode === '') {
            if ($this->pengaturan() === null) {
                throw new DomainException('Masukkan kode cetak untuk mengaktifkan pencetakan.');
            }
            $tersimpan = $builder->where('id', self::SETTING_ID)->update(['whatsapp_operator' => $nomor]);
        } else {
            if (strlen($kode) < 8 || strlen($kode) > 72 || str_contains($kode, "\0")) {
                throw new DomainException('Kode cetak harus berisi 8–72 byte dan berupa teks.');
            }
            $tersimpan = $builder->upsert([
                'id' => self::SETTING_ID,
                'kode_hash' => password_hash($kode, PASSWORD_DEFAULT),
                'whatsapp_operator' => $nomor,
            ]);
        }

        if (! $tersimpan) {
            throw new RuntimeException('Pengaturan akses cetak gagal disimpan.');
        }
    }
}
