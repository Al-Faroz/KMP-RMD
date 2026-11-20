<?php

namespace App\Controllers;

use App\Libraries\EnkripsiKodeCetak;
use App\Models\AksesCetakModel;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

final class PengaturanCetakPublik extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): ResponseInterface
    {
        $pengaturan = model(AksesCetakModel::class)->pengaturanOperator();
        $kode = null;
        $galatDekripsi = false;
        if (($pengaturan['kode_terenkripsi'] ?? '') !== '') {
            try {
                $kode = (new EnkripsiKodeCetak())->dekripsi($pengaturan['kode_terenkripsi']);
            } catch (RuntimeException $e) {
                $galatDekripsi = true;
                log_message('error', 'Kode akses cetak gagal didekripsi: {pesan}', ['pesan' => $e->getMessage()]);
            }
        }

        return $this->response->setHeader('Cache-Control', 'private, no-store')
            ->setBody(view('operator/kode_akses_cetak', [
                'pageTitle' => 'Kode Akses Cetak',
                'activeMenu' => 'kode-akses-cetak',
                'pageScript' => 'rmd/salin-kode-cetak.js',
                'kodeAktif' => ($pengaturan['kode_hash'] ?? '') !== '',
                'kode' => $kode,
                'galatDekripsi' => $galatDekripsi,
                'whatsappOperator' => $pengaturan['whatsapp_operator'] ?? '',
            ]));
    }

    public function buat(): ResponseInterface
    {
        try {
            model(AksesCetakModel::class)->buatKode();
        } catch (RuntimeException $e) {
            log_message('error', 'Kode akses cetak gagal dibuat: {pesan}', ['pesan' => $e->getMessage()]);
            return redirect()->route('kode-akses-cetak', [], 303)
                ->with('galat', 'Kode cetak gagal dibuat. Coba lagi.');
        }

        return redirect()->route('kode-akses-cetak', [], 303)->with('sukses', 'Kode cetak disimpan.');
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
}
