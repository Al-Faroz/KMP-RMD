<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use RuntimeException;
use Throwable;

final class RmdInitialImport
{
    public function __construct(private readonly BaseConnection $db)
    {
    }

    public function run(string $file, bool $dryRun): array
    {
        $book = new RmdWorkbookRows($file);
        $year = $book->angkatan();
        $cohorts = $this->db->table('angkatan');
        $existing = $cohorts->where('tahun_masuk', $year)->get()->getRowArray();
        if ($existing !== null) {
            if ($existing['impor_awal_pada'] === null) {
                throw new RuntimeException("Angkatan {$year} sudah ada tanpa catatan impor awal. Periksa isinya.");
            }

            return ['year' => $year, 'skipped' => true];
        }

        $this->db->transException(true);
        if (! $this->db->transBegin()) {
            throw new RuntimeException('Transaksi impor tidak dapat dimulai.');
        }

        try {
            if (! $cohorts->insert(['tahun_masuk' => $year])) {
                throw new RuntimeException('Angkatan tidak dapat disimpan.');
            }
            $cohortId = (int) $this->db->insertID();
            $lookups = [];
            $counts = ['ditemukan' => 0, 'belum ditemukan' => 0];

            foreach (RmdWorkbookSchema::SHEETS as $sheetName => $mapping) {
                $table = $mapping['table'];
                $key = $mapping['key'];
                $fields = $mapping['fields'];
                $references = RmdWorkbookSchema::REFERENCES[$table] ?? [];
                $columns = [$key, ...array_keys($fields), ...array_column($references, 0)];
                $batch = [];
                $counts[$table] = 0;
                $line = 4;
                foreach ($book->read($sheetName, $key, $columns) as $line => $source) {
                    try {
                        $row = ['id_sumber' => $source[$key], 'angkatan_id' => $cohortId];

                        foreach ($fields as $heading => $type) {
                            $row[RmdWorkbookSchema::column($heading)] = $this->value($type, $source[$heading] ?? null);
                        }

                        foreach ($references as [$heading, $column, $parent, $nullable]) {
                            $value = $source[$heading] ?? null;
                            $row[$column] = $lookups[$parent][$value] ?? null;
                            if ($row[$column] === null && ($value !== null || ! $nullable)) {
                                throw new RuntimeException("Rujukan {$heading} tidak ditemukan.");
                            }
                        }

                        if ($table === 'rapor_semester') {
                            $status = strtolower($row['status_arsip'] ?? '');
                            if (! isset($counts[$status]) || ! in_array($row['semester'], range(1, 6), true)) {
                                throw new RuntimeException('StatusArsip atau Semester tidak valid.');
                            }
                            if ($status === 'ditemukan'
                                && ($row['format_cetak_id'] === null || $row['pengaturan_cetak_id'] === null)) {
                                throw new RuntimeException('Format atau pengaturan cetak belum lengkap.');
                            }
                            $counts[$status]++;
                        }
                    } catch (RuntimeException $error) {
                        throw new RuntimeException("Sheet {$sheetName}, baris {$line}: " . $error->getMessage(), 0, $error);
                    }

                    $batch[] = $row;
                    if (count($batch) === 250) {
                        $this->insertBatch($table, $batch, $sheetName, $line);
                        $batch = [];
                    }
                    $counts[$table]++;
                }

                $this->insertBatch($table, $batch, $sheetName, $line);
                if (in_array($table, ['format_cetak', 'siswa', 'pengaturan_cetak', 'rapor_semester'], true)) {
                    $rows = $this->db->table($table)->select('id, id_sumber')
                        ->where('angkatan_id', $cohortId)->get()->getResultArray();
                    $lookups[$table] = array_column($rows, 'id', 'id_sumber');
                }
            }

            if ($counts['siswa'] === 0 || $counts['rapor_semester'] === 0) {
                throw new RuntimeException('Workbook harus memuat data siswa dan rapor semester.');
            }

            if (! $cohorts->where('id', $cohortId)->update(['impor_awal_pada' => date('Y-m-d H:i:s')])
                || ! $this->db->transStatus()
                || ! ($dryRun ? $this->db->transRollback() : $this->db->transCommit())) {
                throw new RuntimeException('Transaksi impor gagal diselesaikan.');
            }

            return ['year' => $year, 'skipped' => false, 'counts' => $counts];
        } catch (Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    private function insertBatch(string $table, array $rows, string $sheet, int $lastRow): void
    {
        if ($rows === []) {
            return;
        }

        try {
            if ($this->db->table($table)->insertBatch($rows, null, count($rows)) === count($rows)) {
                return;
            }
        } catch (DatabaseException $error) {
            throw new RuntimeException("Sheet {$sheet}, batch hingga baris {$lastRow} ditolak database.", 0, $error);
        }

        throw new RuntimeException("Sheet {$sheet}, batch hingga baris {$lastRow} tidak tersimpan lengkap.");
    }

    private function value(string $type, ?string $value): int|string|null
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => preg_match('/^-?\d+$/D', $value) === 1 ? (int) $value : throw new RuntimeException('Bilangan bulat tidak valid.'),
            'number' => is_numeric($value) ? $value : throw new RuntimeException('Angka tidak valid.'),
            'date' => $this->date($value),
            default => $value,
        };
    }

    private function date(string $value): string
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new RuntimeException('Tanggal tidak valid.');
        }

        return $value;
    }
}
