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
        $pengaturan = model(AksesCetakModel::class)->pengaturan();

        return view('operator/kode_akses_cetak', [
            'pageTitle' => 'Kode Akses Cetak',
            'activeMenu' => 'kode-akses-cetak',
            'whatsappOperator' => $pengaturan['whatsapp_operator'] ?? '',
            'kodeAktif' => $pengaturan !== null,
        ]);
    }

    public function simpan(): ResponseInterface
    {
        $nomor = $this->request->getPost('whatsapp_operator');
        $kode = $this->request->getPost('kode_cetak');

        try {
            if (! is_string($nomor) || ! is_string($kode)) {
                throw new DomainException('Isian pengaturan cetak tidak valid.');
            }
            model(AksesCetakModel::class)->atur($nomor, $kode);
        } catch (DomainException $e) {
            return redirect()->route('kode-akses-cetak', [], 303)->with('galat', $e->getMessage());
        } catch (RuntimeException) {
            return redirect()->route('kode-akses-cetak', [], 303)
                ->with('galat', 'Pengaturan gagal disimpan. Coba lagi.');
        }

        return redirect()->route('kode-akses-cetak', [], 303)->with('sukses', 'Pengaturan akses cetak disimpan.');
    }
}
