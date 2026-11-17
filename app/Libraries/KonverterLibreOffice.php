<?php

namespace App\Libraries;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class KonverterLibreOffice
{
    private const BATAS_DETIK = 120;

    public function folderSementara(): string
    {
        $folder = WRITEPATH . 'cache/khs-' . bin2hex(random_bytes(12));
        if (! @mkdir($folder, 0700, true)) {
            throw new GagalCetakKhs('Folder sementara cetak KHS tidak dapat dibuat.');
        }

        return $folder;
    }

    public function konversi(string $folder, array $dokumen): void
    {
        $biner = $this->biner();
        $batasPhp = (int) ini_get('max_execution_time');
        if (function_exists('set_time_limit') && $batasPhp > 0 && $batasPhp < self::BATAS_DETIK + 15) {
            set_time_limit(self::BATAS_DETIK + 15);
        }

        $profil = str_replace('\\', '/', $folder . '/profil');
        $uri = str_replace(['%2F', '%3A'], ['/', ':'], rawurlencode($profil));
        $uri = 'file://' . (str_starts_with($uri, '/') ? '' : '/') . $uri;
        $log = $folder . '/konversi.log';
        $perintah = [
            $biner, '-env:UserInstallation=' . $uri, '--headless', '--convert-to', 'pdf:writer_pdf_Export',
            '--outdir', $folder, ...$dokumen,
        ];
        $proses = @proc_open($perintah, [
            0 => ['pipe', 'r'],
            1 => ['file', $log, 'a'],
            2 => ['file', $log, 'a'],
        ], $pipa, $folder, null, ['bypass_shell' => true]);
        if (! is_resource($proses)) {
            throw new GagalCetakKhs('LibreOffice tidak dapat dijalankan untuk cetak KHS.');
        }

        fclose($pipa[0]);
        $mulai = hrtime(true);
        try {
            do {
                $status = proc_get_status($proses);
                if (! $status['running']) {
                    break;
                }
                if ((hrtime(true) - $mulai) / 1e9 >= self::BATAS_DETIK) {
                    proc_terminate($proses, 9);
                    throw new GagalCetakKhs('Konversi KHS melewati batas waktu. Coba cetak satu semester atau satu rombel.');
                }
                usleep(50000);
            } while (true);
        } finally {
            proc_close($proses);
        }

        foreach ($dokumen as $docx) {
            $pdf = $folder . '/' . pathinfo($docx, PATHINFO_FILENAME) . '.pdf';
            if ($status['exitcode'] !== 0 || ! is_file($pdf) || file_get_contents($pdf, false, null, 0, 5) !== '%PDF-') {
                throw new GagalCetakKhs('LibreOffice gagal membuat PDF KHS. Periksa instalasi LibreOffice dan izin folder sementara.');
            }
        }
    }

    public function bersihkan(string $folder): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $path = $file->getPathname();
            if ($file->isDir() && ! $file->isLink()) {
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($folder);
    }

    private function biner(): string
    {
        if (! function_exists('proc_open')) {
            throw new GagalCetakKhs('Fungsi PHP proc_open perlu aktif untuk cetak KHS.');
        }

        $diatur = trim((string) env('rmd.libreOfficePath', ''));
        if ($diatur !== '') {
            $calon = [$diatur];
        } elseif (PHP_OS_FAMILY === 'Windows') {
            $calon = array_map(
                static fn ($path): string => $path . '/LibreOffice/program/soffice.com',
                array_filter([getenv('ProgramFiles'), getenv('ProgramFiles(x86)'), getenv('ProgramW6432')])
            );
        } else {
            $calon = array_map(static fn ($path): string => $path . '/soffice', explode(PATH_SEPARATOR, getenv('PATH') ?: ''));
        }

        foreach ($calon as $path) {
            if (is_file($path) && (PHP_OS_FAMILY === 'Windows' || is_executable($path))) {
                return realpath($path) ?: $path;
            }
        }

        throw new GagalCetakKhs('LibreOffice untuk cetak KHS belum ditemukan. Instal LibreOffice atau atur rmd.libreOfficePath pada .env.');
    }
}
