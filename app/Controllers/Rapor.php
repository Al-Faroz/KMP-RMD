<?php

namespace App\Controllers;

use App\Libraries\RaporExcel;
use App\Libraries\RaporFields;
use App\Libraries\RaporPerubahan;
use App\Models\RaporPemutakhiranModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

final class Rapor extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function edit(string $id, string $semester): string
    {
        [$siswa, $snapshot] = $this->target($id, $semester);

        return $this->form($siswa, (int) $semester, $snapshot, $snapshot);
    }

    public function simpan(string $id, string $semester): ResponseInterface
    {
        [$siswa, $snapshot] = $this->target($id, $semester);
        $model = model(RaporPemutakhiranModel::class);
        $masukan = [];
        $versi = $this->request->getPost('versi');
        try {
            if ($this->request->getPost('form_lengkap') !== '1') {
                throw new DomainException('Formulir tidak terkirim lengkap. Buka kembali Edit Rapor dan simpan perubahan dalam beberapa kali pengiriman.');
            }
            foreach (RaporFields::LABELS as $bagian => $label) {
                $masukan[$bagian] = $this->request->getPost($bagian) ?? [];
                if (! is_array($masukan[$bagian])) {
                    throw new DomainException("Isian {$label} tidak valid.");
                }
            }

            if (! is_string($versi) || ! hash_equals($model->versi($snapshot), $versi)) {
                throw new DomainException('Rapor sudah berubah. Buka kembali halaman semester sebelum menyunting.');
            }

            $hasil = (new RaporPerubahan())->siapkan($snapshot, $masukan, false);
            if ($hasil['preview'] !== []) {
                $model->simpan($siswa, (int) $semester, $versi, $hasil['patch']);
            }
        } catch (DomainException $error) {
            return $this->response->setStatusCode(422)->setBody(
                $this->form($siswa, (int) $semester, $snapshot, $masukan, $error->getMessage(),
                    is_string($versi ?? null) ? $versi : '')
            );
        }

        return $this->kembali($id, $semester, 'pesan_rapor',
            $hasil['preview'] === [] ? 'Tidak ada perubahan rapor.' : 'Perubahan rapor tersimpan.');
    }

    public function excel(string $id, string $semester): ResponseInterface
    {
        [$siswa, $snapshot] = $this->target($id, $semester);
        $kode = $this->request->getGet('format');
        $kode = is_string($kode) && isset($snapshot['format'][$kode]) ? $kode : null;
        $isi = (new RaporExcel())->unduh($siswa, (int) $semester, $snapshot, $kode);

        return $this->response->download("RMD-Siswa-{$id}-Semester-{$semester}.xlsx", $isi, true);
    }

    public function pratinjau(string $id, string $semester): ResponseInterface
    {
        [$siswa, $snapshot] = $this->target($id, $semester);
        session()->removeTempdata('pratinjau_rapor');
        $file = $this->request->getFile('workbook');
        if ($file === null || ! $file->isValid() || $file->getSize() > 1024 * 1024
            || strtolower($file->getClientExtension()) !== 'xlsx') {
            return $this->kembali($id, $semester, 'galat_rapor', 'Pilih file .xlsx untuk satu rapor, maksimal 1 MB.');
        }

        try {
            $masukan = (new RaporExcel())->baca($file->getTempName(), $siswa, (int) $semester, $snapshot['rapor']);
            $hasil = (new RaporPerubahan())->siapkan($snapshot, $masukan, true);
        } catch (DomainException|SpreadsheetException $error) {
            return $this->kembali($id, $semester, 'galat_rapor',
                $error instanceof DomainException ? $error->getMessage() : 'File tidak dapat dibaca sebagai template Excel RMD.');
        }

        $token = '';
        if ($hasil['preview'] !== []) {
            $token = bin2hex(random_bytes(24));
            // Keep one pending preview per operator session.
            session()->setTempdata('pratinjau_rapor', [
                'token' => $token,
                'id' => $id,
                'semester' => $semester,
                'versi' => model(RaporPemutakhiranModel::class)->versi($snapshot),
                'patch' => $hasil['patch'],
            ], 1800);
        }

        return $this->response->setHeader('Cache-Control', 'no-store')->setBody(
            $this->halaman('pratinjau', 'Pratinjau Impor', $siswa, (int) $semester, [
                'perubahan' => $hasil['preview'],
                'token' => $token,
            ])
        );
    }

    public function konfirmasi(string $id, string $semester): ResponseInterface
    {
        [$siswa] = $this->target($id, $semester);
        $token = $this->request->getPost('token');
        $pending = session()->getTempdata('pratinjau_rapor');
        if (! is_string($token) || ! is_array($pending) || $pending['id'] !== $id
            || $pending['semester'] !== $semester || ! hash_equals($pending['token'], $token)) {
            return $this->kembali($id, $semester, 'galat_rapor', 'Pratinjau tidak tersedia. Unggah ulang file untuk semester ini.');
        }

        try {
            model(RaporPemutakhiranModel::class)->simpan($siswa, (int) $semester, $pending['versi'], $pending['patch']);
        } catch (DomainException $error) {
            session()->removeTempdata('pratinjau_rapor');

            return $this->kembali($id, $semester, 'galat_rapor', $error->getMessage());
        }

        session()->removeTempdata('pratinjau_rapor');

        return $this->kembali($id, $semester, 'pesan_rapor', 'Perubahan Excel tersimpan.');
    }

    private function target(string $id, string $semester): array
    {
        if (! in_array($semester, ['1', '2', '3', '4', '5', '6'], true)) {
            throw PageNotFoundException::forPageNotFound('Semester tidak ditemukan.');
        }
        $siswa = model(SiswaModel::class)->identitas($id);
        if ($siswa === null) {
            throw PageNotFoundException::forPageNotFound('Siswa tidak ditemukan.');
        }

        return [$siswa, model(RaporPemutakhiranModel::class)->snapshot($siswa, (int) $semester)];
    }

    private function form(array $siswa, int $semester, array $snapshot, array $masukan, string $galat = '', ?string $versi = null): string
    {
        return $this->halaman('edit', 'Edit Rapor', $siswa, $semester, [
            'snapshot' => $snapshot,
            'masukan' => $masukan,
            'galat' => $galat,
            'versi' => $versi ?? model(RaporPemutakhiranModel::class)->versi($snapshot),
            'pageScript' => 'rmd/edit-rapor.js',
        ]);
    }

    private function halaman(string $file, string $title, array $siswa, int $semester, array $data): string
    {
        return view('operator/rapor/' . $file, $data + [
            'pageTitle' => $title,
            'activeMenu' => 'data-siswa',
            'siswa' => $siswa,
            'semester' => $semester,
        ]);
    }

    private function kembali(string $id, string $semester, string $jenis, string $pesan): ResponseInterface
    {
        return redirect()->route('detail-semester', [$id, $semester], 303)->with($jenis, $pesan)->withCookies();
    }
}
