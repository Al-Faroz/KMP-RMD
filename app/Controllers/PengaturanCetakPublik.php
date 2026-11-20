<?php

namespace App\Controllers;

use App\Models\AksesCetakModel;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

final class PengaturanCetakPublik extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        return $this->halaman();
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
            ->setBody($this->halaman($kode));
    }

    public function simpanWhatsApp(): ResponseInterface
    {
        $nomor = $this->request->getPost('whatsapp_operator');
        try {
            model(AksesCetakModel::class)->simpanWhatsApp(is_string($nomor) ? $nomor : '');
        } catch (DomainException $e) {
            return redirect()->route('kode-akses-cetak', [], 303)->with('galat', $e->getMessage());
        } catch (RuntimeException) {
            return redirect()->route('kode-akses-cetak', [], 303)
                ->with('galat', 'Nomor WhatsApp gagal disimpan. Coba lagi.');
        }

        return redirect()->route('kode-akses-cetak', [], 303)->with('sukses', 'Nomor WhatsApp disimpan.');
    }

    private function halaman(?string $kodeBaru = null): string
    {
        $pengaturan = model(AksesCetakModel::class)->pengaturan();

        return view('operator/kode_akses_cetak', [
            'pageTitle' => 'Kode Akses Cetak',
            'activeMenu' => 'kode-akses-cetak',
            'pageScript' => 'rmd/salin-kode-cetak.js',
            'kodeAktif' => ($pengaturan['kode_hash'] ?? '') !== '',
            'kodeBaru' => $kodeBaru,
            'whatsappOperator' => $pengaturan['whatsapp_operator'] ?? '',
        ]);
    }
}
