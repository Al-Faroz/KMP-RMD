<?php

namespace App\Libraries;

use CodeIgniter\Encryption\EncrypterInterface;
use Config\Encryption;
use Config\Services;
use RuntimeException;

final class EnkripsiKodeCetak
{
    public function enkripsi(#[\SensitiveParameter] string $kode): string
    {
        return base64_encode($this->encrypter(true)->encrypt($kode));
    }

    public function dekripsi(string $data): string
    {
        $ciphertext = base64_decode($data, true);
        if ($ciphertext === false || $ciphertext === '') {
            throw new RuntimeException('Kode cetak terenkripsi tidak valid.');
        }

        return $this->encrypter(false)->decrypt($ciphertext);
    }

    private function encrypter(bool $buatKunci): EncrypterInterface
    {
        $config = clone config(Encryption::class);
        if ($config->key === '') {
            $config->key = $this->kunciLokal($buatKunci);
        }

        return Services::encrypter($config);
    }

    private function kunciLokal(bool $buatKunci): string
    {
        $path = WRITEPATH . 'kode-cetak.key';
        $file = @fopen($path, $buatKunci ? 'c+b' : 'rb');
        if ($file === false) {
            throw new RuntimeException('Berkas kunci kode cetak tidak dapat dibuka.');
        }

        try {
            if (! flock($file, $buatKunci ? LOCK_EX : LOCK_SH)) {
                throw new RuntimeException('Berkas kunci kode cetak tidak dapat dikunci.');
            }

            $kunci = stream_get_contents($file);
            if ($kunci === '' && $buatKunci) {
                $kunci = random_bytes(32);
                if (PHP_OS_FAMILY !== 'Windows' && ! @chmod($path, 0600)) {
                    throw new RuntimeException('Izin berkas kunci kode cetak gagal diatur.');
                }
                if (fwrite($file, $kunci) !== 32 || ! fflush($file)) {
                    ftruncate($file, 0);
                    throw new RuntimeException('Kunci kode cetak gagal disimpan.');
                }
            }

            if ($kunci === false || strlen($kunci) !== 32) {
                throw new RuntimeException('Berkas kunci kode cetak tidak valid.');
            }

            return $kunci;
        } finally {
            fclose($file);
        }
    }
}
