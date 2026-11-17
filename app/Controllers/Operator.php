<?php

namespace App\Controllers;

use App\Libraries\CetakRapor;
use App\Models\RaporSemesterModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Operator extends BaseController
{
    protected $helpers = ['url'];

    public function cetakRombel(): ResponseInterface
    {
        $model = model(RaporSemesterModel::class);
        $pilihan = $this->pilihanRombel($model);
        $anggota = $pilihan['rombel'] === '' ? [] : $model->anggotaRombel($pilihan['angkatan'], $pilihan['semester'], $pilihan['rombel']);
        $tersedia = $this->raporTersedia($anggota, new CetakRapor());

        return $this->response->setHeader('Cache-Control', 'private, no-store')->setBody(view('operator/cetak_rombel', $pilihan + [
            'pageTitle' => 'Cetak Rombel',
            'activeMenu' => 'cetak-rombel',
            'pageScript' => 'rmd/cetak-rombel.js',
            'daftarAngkatan' => model(SiswaModel::class)->daftarAngkatan(),
            'anggota' => $anggota,
            'tersedia' => array_column($tersedia, null, 'id'),
        ]));
    }

    public function pdfRombel(): ResponseInterface
    {
        $model = model(RaporSemesterModel::class);
        ['angkatan' => $angkatan, 'semester' => $semester, 'rombel' => $rombel] = $this->pilihanRombel($model);
        if ($rombel === '') {
            throw PageNotFoundException::forPageNotFound('Pilihan angkatan, semester, dan rombel tidak ditemukan.');
        }

        $cetak = new CetakRapor();
        $arsip = $this->raporTersedia($model->anggotaRombel($angkatan, $semester, $rombel), $cetak);
        if ($arsip === []) {
            throw PageNotFoundException::forPageNotFound('Belum ada rapor yang dapat dicetak untuk rombel ini.');
        }

        $namaRombel = preg_replace('/[^A-Za-z0-9_-]+/', '-', $rombel);
        $nama = "RMD-Rapor-Rombel-{$angkatan}-{$namaRombel}-Semester-{$semester}.pdf";

        return $this->response->setContentType('application/pdf')->setHeader('Cache-Control', 'private, no-store')
            ->setHeader('Content-Disposition', 'inline; filename="' . $nama . '"')
            ->setBody($cetak->buat("Rapor {$rombel} Semester {$semester} Angkatan {$angkatan}", $arsip, $model));
    }

    private function pilihanRombel(RaporSemesterModel $model): array
    {
        $tahun = $this->request->getGet('angkatan');
        $nomor = $this->request->getGet('semester');
        $rombel = $this->request->getGet('rombel');
        $angkatan = is_string($tahun) && preg_match('/^[0-9]{4}$/D', $tahun) === 1 ? (int) $tahun : 0;
        $semester = in_array($nomor, ['1', '2', '3', '4', '5', '6'], true) ? (int) $nomor : 0;
        $daftarRombel = $angkatan === 0 || $semester === 0 ? [] : $model->daftarRombel($angkatan, $semester);
        $valid = is_string($rombel) && in_array($rombel, $daftarRombel, true);

        return [
            'angkatan' => $angkatan,
            'semester' => $semester,
            'rombel' => $valid ? $rombel : '',
            'daftarRombel' => $daftarRombel,
            'rombelTidakValid' => $rombel !== null && $rombel !== '' && ! $valid,
        ];
    }

    private function raporTersedia(array $anggota, CetakRapor $cetak): array
    {
        return array_filter($anggota, static fn (array $rapor): bool =>
            $rapor['status_arsip'] === 'Ditemukan' && $cetak->mendukung($rapor['kode_format']));
    }
}
