<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

final class CreateOperatorAccounts extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 255],
            'username' => ['type' => 'VARCHAR', 'constraint' => 64],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username', 'uq_operator_username');

        if (! $this->forge->createTable('operator', true, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException('Tabel operator gagal dibuat.');
        }
    }

    public function down(): void
    {
        if ($this->db->table('operator')->countAllResults() > 0) {
            throw new RuntimeException('Rollback dibatalkan karena tabel operator berisi akun.');
        }

        $this->forge->dropTable('operator', true);
    }
}
