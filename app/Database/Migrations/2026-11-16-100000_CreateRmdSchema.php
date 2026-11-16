<?php

namespace App\Database\Migrations;

use App\Libraries\RmdWorkbookSchema;
use CodeIgniter\Database\Migration;
use RuntimeException;
use Throwable;

final class CreateRmdSchema extends Migration
{
    private const ID = ['type' => 'INT', 'unsigned' => true];
    private const SOURCE_ID = ['type' => 'VARCHAR', 'constraint' => 64];

    private const FIELD_TYPES = [
        'string' => ['type' => 'VARCHAR', 'constraint' => 255],
        'text' => ['type' => 'TEXT'],
        'date' => ['type' => 'DATE'],
        'integer' => ['type' => 'INT'],
        'number' => ['type' => 'DECIMAL', 'constraint' => '28,15'],
    ];

    public function up(): void
    {
        foreach ($this->tableNames() as $table) {
            if ($this->db->tableExists($table, false)) {
                throw new RuntimeException("Tabel {$table} sudah ada. Periksa skema dan riwayat migrasi.");
            }
        }

        $created = [];
        try {
            $this->forge->addField([
                'tahun_masuk' => ['type' => 'SMALLINT', 'unsigned' => true],
                'impor_awal_pada' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('tahun_masuk', false, true, 'uq_angkatan_tahun');
            $this->create('angkatan');
            $created[] = 'angkatan';

            foreach (RmdWorkbookSchema::SHEETS as $sheet) {
                $table = $sheet['table'];
                $this->createWorkbookTable($table, $sheet['fields']);
                $created[] = $table;
            }
        } catch (Throwable $error) {
            // MySQL/MariaDB implicitly commits DDL. Only undo tables created by this run.
            foreach (array_reverse($created) as $table) {
                $this->forge->dropTable($table, true);
            }

            throw $error;
        }
    }

    public function down(): void
    {
        foreach ($this->tableNames() as $table) {
            if ($this->db->tableExists($table, false) && $this->db->table($table)->countAll() > 0) {
                throw new RuntimeException("Rollback dibatalkan karena tabel {$table} berisi data.");
            }
        }

        foreach (array_reverse($this->tableNames()) as $table) {
            $this->forge->dropTable($table, true);
        }
    }

    private function createWorkbookTable(string $table, array $fields): void
    {
        $this->forge->addField(['id_sumber' => self::SOURCE_ID]);

        $this->forge->addField(['angkatan_id' => self::ID]);
        $this->forge->addForeignKey('angkatan_id', 'angkatan', 'id');
        $this->forge->addUniqueKey(['angkatan_id', 'id_sumber'], 'uq_' . $table . '_sumber');

        foreach ($fields as $heading => $type) {
            $required = in_array($heading, ['Nama', 'Semester', 'StatusArsip', 'NamaMapelAsli', 'JenisItem'], true);
            $this->forge->addField([
                RmdWorkbookSchema::column($heading) => self::FIELD_TYPES[$type] + ['null' => ! $required],
            ]);
        }

        foreach (RmdWorkbookSchema::REFERENCES[$table] ?? [] as [, $column, $parent, $nullable]) {
            $this->forge->addField([$column => self::ID + ['null' => $nullable]]);
            $this->forge->addForeignKey($column, $parent, 'id');
        }

        if ($table === 'rapor_semester') {
            $this->forge->addUniqueKey(['siswa_id', 'semester'], 'uq_rapor_siswa_semester');
            $this->forge->addKey(['angkatan_id', 'semester', 'rombel'], false, false, 'idx_rapor_rombel');
        }

        $this->create($table);
    }

    private function create(string $table): void
    {
        $this->forge->addField(['id' => self::ID + ['auto_increment' => true]]);
        $this->forge->addPrimaryKey('id');

        if (! $this->forge->createTable($table, false, ['ENGINE' => 'InnoDB'])) {
            throw new RuntimeException("Gagal membuat tabel {$table}.");
        }
    }

    private function tableNames(): array
    {
        return ['angkatan', ...array_column(RmdWorkbookSchema::SHEETS, 'table')];
    }
}
