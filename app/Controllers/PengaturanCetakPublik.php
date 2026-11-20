<?php

namespace App\Controllers;

use App\Models\AksesCetakModel;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

final class PengaturanCetakPublik extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        return $this->halaman(model(AksesCetakModel::class)->pengaturan() !== null);
    }

    public function buat(): ResponseInterface
    {
        try {
            $kode = model(AksesCetakModel::class)->buatKode();
        } catch (RuntimeException) {
            return redirect()->route('kode-akses-cetak', [], 303)
                ->with('galat', 'Kode cetak gagal dibuat. Coba lagi.');
        }

        return $this->response->setHeader('Cache-Control', 'private, no-store')
            ->setBody($this->halaman(true, $kode));
    }

    private function halaman(bool $kodeAktif, ?string $kodeBaru = null): string
    {
        return view('operator/kode_akses_cetak', [
            'pageTitle' => 'Kode Akses Cetak',
            'activeMenu' => 'kode-akses-cetak',
            'pageScript' => 'rmd/salin-kode-cetak.js',
            'kodeAktif' => $kodeAktif,
            'kodeBaru' => $kodeBaru,
        ]);
    }
}
