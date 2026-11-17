<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class CreateRiwayatArsip extends Migration
{
    public function up(): void
    {
        $id = ['type' => 'INT', 'unsigned' => true];
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => $id,
            'semester' => ['type' => 'TINYINT', 'unsigned' => true],
            'operator_id' => $id,
            'nama_operator' => ['type' => 'VARCHAR', 'constraint' => 255],
            'aksi' => ['type' => 'VARCHAR', 'constraint' => 16],
            'dipulihkan_dari' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'sebelum' => ['type' => 'LONGTEXT'],
            'sesudah' => ['type' => 'LONGTEXT'],
            'waktu' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey(['siswa_id', 'semester', 'id'], false, false, 'idx_riwayat_siswa_semester');
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id');

        if (! $this->forge->createTable('riwayat_arsip', true, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException('Tabel riwayat arsip gagal dibuat.');
        }
    }

    public function down(): void
    {
        if ($this->db->table('riwayat_arsip')->countAll() > 0) {
            throw new RuntimeException('Rollback dibatalkan karena tabel riwayat arsip berisi data.');
        }

        $this->forge->dropTable('riwayat_arsip', true);
    }
}
