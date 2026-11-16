<?php

namespace App\Commands;

use App\Libraries\RmdInitialImport;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

final class RmdImportInitial extends BaseCommand
{
    protected $group = 'RMD';
    protected $name = 'rmd:impor-awal';
    protected $description = 'Memuat satu workbook final angkatan ke skema RMD.';
    protected $usage = 'rmd:impor-awal <path-workbook.xlsx> [--dry-run]';
    protected $options = ['--dry-run' => 'Uji impor dan batalkan transaksi setelah validasi.'];

    public function run(array $params): int
    {
        $file = isset($params[0]) ? realpath($params[0]) : false;
        if ($file === false || ! is_file($file) || strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'xlsx') {
            CLI::error('Berikan path ke satu workbook final .xlsx.');

            return EXIT_ERROR;
        }

        $dryRun = array_key_exists('dry-run', $params) || CLI::getOption('dry-run') !== null;

        try {
            $db = Database::connect('default');
            $result = (new RmdInitialImport($db))->run($file, $dryRun);
            $year = $result['year'];
            if ($result['skipped']) {
                CLI::write("Angkatan {$year} sudah diimpor.");

                return EXIT_SUCCESS;
            }

            CLI::write($dryRun ? "Simulasi angkatan {$year} selesai." : "Impor angkatan {$year} selesai.", 'green');
            foreach ($result['counts'] as $label => $count) {
                CLI::write("{$label}: {$count}");
            }

            return EXIT_SUCCESS;
        } catch (Throwable $error) {
            CLI::error($error->getMessage());

            return EXIT_ERROR;
        }
    }
}
