<?php

namespace App\Models;

use App\Libraries\EnkripsiKodeCetak;
use CodeIgniter\Model;
use DomainException;
use RuntimeException;

final class AksesCetakModel extends Model
{
    private const SETTING_ID = 1;

    protected $table = 'akses_cetak';

    public function pengaturan(): ?array
    {
        return $this->select('kode_hash, whatsapp_operator')->find(self::SETTING_ID);
    }

    public function pengaturanOperator(): ?array
    {
        return $this->find(self::SETTING_ID);
    }

    public function buatKode(): void
    {
        $kode = strtoupper(bin2hex(random_bytes(8)));
        $tersimpan = $this->builder()->upsert([
            'id' => self::SETTING_ID,
            'kode_hash' => password_hash($kode, PASSWORD_DEFAULT),
            'kode_terenkripsi' => (new EnkripsiKodeCetak())->enkripsi($kode),
        ]);

        if (! $tersimpan) {
            throw new RuntimeException('Pengaturan akses cetak gagal disimpan.');
        }
    }

    public function simpanWhatsApp(string $nomor): void
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

        $tersimpan = $this->builder()->updateFields(['whatsapp_operator'])->upsert([
            'id' => self::SETTING_ID,
            'kode_hash' => '',
            'whatsapp_operator' => $nomor,
        ]);

        if ($tersimpan === false) {
            throw new RuntimeException('Nomor WhatsApp gagal disimpan.');
        }
    }
}
