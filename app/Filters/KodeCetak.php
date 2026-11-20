<?php

namespace App\Filters;

use App\Models\AksesCetakModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\CetakPublik;

final class KodeCetak implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?ResponseInterface
    {
        $throttler = service('throttler');
        $batas = 'rmd_cetak_' . hash('sha256', $request->getIPAddress());

        if (! $throttler->check($batas, 8, 60)) {
            return $this->galat(429, 'Terlalu banyak percobaan. Coba kembali sebentar.')
                ->setHeader('Retry-After', (string) $throttler->getTokenTime());
        }

        $kode = $request->getPost('kode_cetak');
        if (! is_string($kode) || $kode === '' || strlen($kode) > 72) {
            return $this->galat(403, 'Kode akses cetak salah. Periksa kode dan coba lagi.');
        }

        $kodeUniversal = config(CetakPublik::class)->kodeUniversal;
        if ($kodeUniversal !== '' && hash_equals($kodeUniversal, $kode)) {
            $throttler->remove($batas);

            return null;
        }

        $pengaturan = model(AksesCetakModel::class)->pengaturan();
        $hash = $pengaturan['kode_hash'] ?? '';
        if ($hash === '' && $kodeUniversal === '') {
            return $this->galat(503, 'Akses cetak belum diatur. Hubungi operator madrasah.');
        }
        if ($hash === '' || ! password_verify($kode, $hash)) {
            return $this->galat(403, 'Kode akses cetak salah. Periksa kode dan coba lagi.');
        }
        if ((int) ($pengaturan['kode_berlaku_hingga'] ?? 0) <= time()) {
            return $this->galat(403, 'Kode cetak sudah kedaluwarsa. Minta kode baru ke operator madrasah.');
        }

        $throttler->remove($batas);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function galat(int $status, string $pesan): ResponseInterface
    {
        return service('response')->setStatusCode($status)
            ->setHeader('Cache-Control', 'private, no-store')
            ->setContentType('text/plain')
            ->setBody($pesan);
    }
}
