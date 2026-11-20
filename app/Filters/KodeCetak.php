<?php

namespace App\Filters;

use App\Models\AksesCetakModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

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

        $hash = model(AksesCetakModel::class)->pengaturan()['kode_hash'] ?? '';
        if ($hash === '') {
            return $this->galat(503, 'Akses cetak belum diatur. Hubungi operator madrasah.');
        }

        $kode = $request->getPost('kode_cetak');
        if (! is_string($kode) || strlen($kode) > 72 || ! password_verify($kode, $hash)) {
            return $this->galat(403, 'Kode akses cetak salah. Periksa kode dan coba lagi.');
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
