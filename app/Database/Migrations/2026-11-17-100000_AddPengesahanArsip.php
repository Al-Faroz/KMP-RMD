<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class AddPengesahanArsip extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('pengesahan_arsip', 'rapor_semester')
            && ! $this->forge->addColumn('rapor_semester', ['pengesahan_arsip' => ['type' => 'TEXT', 'null' => true]])) {
            throw new RuntimeException('Kolom pengesahan arsip gagal ditambahkan.');
        }
    }

    public function down(): void
    {
        if (! $this->db->fieldExists('pengesahan_arsip', 'rapor_semester')) {
            return;
        }
        if ($this->db->table('rapor_semester')->where('pengesahan_arsip IS NOT NULL', null, false)->countAllResults() > 0) {
            throw new RuntimeException('Rollback dibatalkan karena terdapat koreksi pengesahan arsip.');
        }
        if (! $this->forge->dropColumn('rapor_semester', 'pengesahan_arsip')) {
            throw new RuntimeException('Kolom pengesahan arsip gagal dihapus.');
        }
    }
}
