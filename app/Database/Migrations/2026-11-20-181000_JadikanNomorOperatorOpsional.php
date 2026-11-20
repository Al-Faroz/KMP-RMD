<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class JadikanNomorOperatorOpsional extends Migration
{
    public function up(): void
    {
        if (! $this->forge->modifyColumn('akses_cetak', [
            'whatsapp_operator' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
        ])) {
            throw new RuntimeException('Kolom nomor operator gagal disesuaikan.');
        }
    }

    public function down(): void
    {
        $this->db->table('akses_cetak')->where('whatsapp_operator', null)
            ->update(['whatsapp_operator' => '']);

        if (! $this->forge->modifyColumn('akses_cetak', [
            'whatsapp_operator' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => false],
        ])) {
            throw new RuntimeException('Kolom nomor operator gagal dipulihkan.');
        }
    }
}
