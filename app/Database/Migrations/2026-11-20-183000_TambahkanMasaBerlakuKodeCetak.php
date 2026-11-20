<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class TambahkanMasaBerlakuKodeCetak extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('kode_berlaku_hingga', 'akses_cetak')
            && ! $this->forge->addColumn('akses_cetak', [
                'kode_berlaku_hingga' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            ])
        ) {
            throw new RuntimeException('Kolom masa berlaku kode cetak gagal ditambahkan.');
        }

        $tersimpan = $this->db->table('akses_cetak')
            ->where('kode_hash !=', '')
            ->where('kode_berlaku_hingga', null)
            ->update(['kode_berlaku_hingga' => time() + 86400]);
        if (! $tersimpan) {
            throw new RuntimeException('Masa berlaku kode cetak yang sudah ada gagal disimpan.');
        }
    }

    public function down(): void
    {
        if (! $this->forge->dropColumn('akses_cetak', 'kode_berlaku_hingga')) {
            throw new RuntimeException('Kolom masa berlaku kode cetak gagal dihapus.');
        }
    }
}
