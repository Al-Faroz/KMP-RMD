<?php

namespace App\Commands;

use App\Models\OperatorModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Exceptions\DatabaseException;
use DomainException;

final class RmdBuatOperator extends BaseCommand
{
    protected $group = 'RMD';
    protected $name = 'rmd:operator-buat';
    protected $description = 'Buat akun operator dengan membaca kata sandi dari stdin.';
    protected $usage = 'rmd:operator-buat "Nama Lengkap" username';

    public function run(array $params): int
    {
        if (count($params) !== 2 || ! isset($params[0], $params[1]) || stream_isatty(STDIN)) {
            CLI::error('Berikan nama lengkap dan username. Kirim kata sandi melalui stdin.');

            return EXIT_ERROR;
        }
        $password = rtrim((string) stream_get_contents(STDIN, 75), "\r\n");

        try {
            model(OperatorModel::class)->buatAkun($params[0], $params[1], $password);
        } catch (DomainException $error) {
            CLI::error($error->getMessage());

            return EXIT_ERROR;
        } catch (DatabaseException) {
            CLI::error('Akun gagal dibuat. Periksa migrasi database dan ketersediaan username.');

            return EXIT_ERROR;
        }

        CLI::write('Akun operator berhasil dibuat.', 'green');

        return EXIT_SUCCESS;
    }
}
