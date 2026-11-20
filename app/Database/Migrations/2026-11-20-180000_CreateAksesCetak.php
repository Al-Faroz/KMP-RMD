<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class CreateAksesCetak extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'TINYINT', 'unsigned' => true],
            'kode_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'whatsapp_operator' => ['type' => 'VARCHAR', 'constraint' => 15],
        ]);
        $this->forge->addPrimaryKey('id');

        if (! $this->forge->createTable('akses_cetak', true, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException('Tabel akses cetak gagal dibuat.');
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('akses_cetak', true);
    }
}
