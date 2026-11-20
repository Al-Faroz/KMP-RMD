<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class SimpanKodeCetakTerenkripsi extends Migration
{
    public function up(): void
    {
        if (! $this->forge->addColumn('akses_cetak', [
            'kode_terenkripsi' => ['type' => 'TEXT', 'null' => true],
        ])) {
            throw new RuntimeException('Kolom kode cetak terenkripsi gagal ditambahkan.');
        }
    }

    public function down(): void
    {
        if (! $this->forge->dropColumn('akses_cetak', 'kode_terenkripsi')) {
            throw new RuntimeException('Kolom kode cetak terenkripsi gagal dihapus.');
        }
    }
}
