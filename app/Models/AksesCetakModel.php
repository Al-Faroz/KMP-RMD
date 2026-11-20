<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

final class AksesCetakModel extends Model
{
    private const SETTING_ID = 1;

    protected $table = 'akses_cetak';

    public function pengaturan(): ?array
    {
        return $this->select('kode_hash')->find(self::SETTING_ID);
    }

    public function buatKode(): string
    {
        $kode = strtoupper(bin2hex(random_bytes(8)));
        $tersimpan = $this->builder()->upsert([
            'id' => self::SETTING_ID,
            'kode_hash' => password_hash($kode, PASSWORD_DEFAULT),
        ]);

        if (! $tersimpan) {
            throw new RuntimeException('Pengaturan akses cetak gagal disimpan.');
        }

        return $kode;
    }
}
