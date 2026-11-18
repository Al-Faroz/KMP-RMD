<?php

namespace App\Controllers;

use App\Libraries\CetakDokumenSiswa;
use App\Libraries\CetakRapor;
use App\Libraries\CetakTranskrip;
use App\Libraries\PengesahanRapor;
use App\Models\RaporSemesterModel;
use App\Models\RiwayatArsipModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

final class Siswa extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        [$kata, $angkatan] = $this->filterPencarian();
        $model = model(SiswaModel::class);
        $siswa = $model->cari($kata, $angkatan);

        return $this->halaman('siswa', 'Data Siswa', [
            'siswa' => $siswa,
            'daftarAngkatan' => $model->daftarAngkatan(),
            'kata' => $kata,
            'angkatan' => $angkatan,
            'pager' => $model->pager->only(['q', 'angkatan']),
            'pageScript' => 'rmd/siswa-search.js',
        ]);
    }

    public function saran(): ResponseInterface
    {
        [$kata, $angkatan] = $this->filterPencarian();
        $hasil = mb_strlen($kata) < 2 ? [] : model(SiswaModel::class)->saran($kata, $angkatan);
        $saran = array_map(static fn (array $siswa): array => [
            'nama' => $siswa['nama'],
            'angkatan' => $siswa['tahun_masuk'],
            'url' => url_to('detail-siswa', $siswa['id']),
        ], $hasil);

        return $this->response->setHeader('Cache-Control', 'no-store')->setJSON($saran);
    }

    public function show(string $id): string
    {
        $siswa = $this->temukanSiswa($id);
        $riwayat = model(RaporSemesterModel::class)->riwayat($id);
        $cetak = new CetakRapor();

        return $this->halaman('siswa/detail', 'Detail Siswa', [
            'siswa' => $siswa,
            'riwayat' => $riwayat,
            'adaRaporTersedia' => array_filter($riwayat, static fn (array $rapor): bool =>
                $rapor['status_arsip'] === 'Ditemukan' && $cetak->mendukung($rapor['kode_format'])) !== [],
        ]);
    }

    public function transkrip(string $id): ResponseInterface
    {
        $siswa = $this->temukanSiswa($id);
        $arsip = model(RaporSemesterModel::class)->arsipTranskrip($id);
        return $this->pdf('Transkrip-' . $siswa['id'], (new CetakTranskrip())->buat($siswa, $arsip));
    }

    public function sampul(string $id): ResponseInterface
    {
        $siswa = $this->temukanSiswa($id);

        return $this->pdf('Sampul-' . $siswa['id'], (new CetakDokumenSiswa())->sampul($siswa));
    }

    public function cetakIdentitas(string $id): ResponseInterface
    {
        $siswa = $this->temukanSiswa($id);
        $pengaturan = model(RaporSemesterModel::class)->pengaturanIdentitas($id);

        return $this->pdf('Identitas-' . $siswa['id'], (new CetakDokumenSiswa())->identitas($siswa, $pengaturan));
    }

    public function lembarNilai(string $id, string $semester): ResponseInterface
    {
        $this->validasiSemester($semester);
        $siswa = $this->temukanSiswa($id);
        $model = model(RaporSemesterModel::class);
        $rapor = $model->detail($id, (int) $semester);
        if (($rapor['status_arsip'] ?? null) !== 'Ditemukan') {
            throw PageNotFoundException::forPageNotFound('Arsip rapor semester ini belum ditemukan.');
        }

        $nilai = $model->nilai((string) $rapor['id']);
        $pengaturan = $model->pengaturanCetak($rapor['pengaturan_cetak_id']);
        $pdf = (new CetakDokumenSiswa())->nilai($siswa, $rapor, $nilai, $pengaturan);

        return $this->pdf('Lembar-Nilai-' . $siswa['id'] . '-Semester-' . $semester, $pdf);
    }

    public function cetakRapor(string $id, string $semester): ResponseInterface
    {
        $this->validasiSemester($semester);
        $siswa = $this->temukanSiswa($id);
        $model = model(RaporSemesterModel::class);
        $rapor = $model->detail($id, (int) $semester);
        if (($rapor['status_arsip'] ?? null) !== 'Ditemukan') {
            throw PageNotFoundException::forPageNotFound('Arsip rapor semester ini belum ditemukan.');
        }

        $cetak = new CetakRapor();
        if (! $cetak->mendukung($rapor['kode_format'])) {
            return $this->response->setStatusCode(422)->setContentType('text/plain')
                ->setBody('Format cetak rapor semester ini belum tersedia. Periksa format di Edit Rapor.');
        }

        $pdf = $cetak->buat('Rapor Semester ' . $semester, [$rapor + $siswa], $model);

        return $this->pdf('Rapor-' . $siswa['id'] . '-Semester-' . $semester, $pdf);
    }

    public function cetakSemuaSemester(string $id): ResponseInterface
    {
        $siswa = $this->temukanSiswa($id);
        $model = model(RaporSemesterModel::class);
        $cetak = new CetakRapor();
        $arsip = array_filter($model->arsipSiswa($id), static fn (array $rapor): bool =>
            $rapor['status_arsip'] === 'Ditemukan' && $cetak->mendukung($rapor['kode_format']));
        if ($arsip === []) {
            throw PageNotFoundException::forPageNotFound('Belum ada rapor semester yang dapat dicetak.');
        }

        return $this->pdf('Rapor-Semua-Semester-' . $siswa['id'], $cetak->buat('Rapor Semua Semester', $arsip, $model));
    }

    public function semester(string $id, string $semester): string
    {
        $this->validasiSemester($semester);
        $siswa = $this->temukanSiswa($id);
        $model = model(RaporSemesterModel::class);
        $rapor = $model->detail($id, (int) $semester);

        return $this->halaman('siswa/semester', 'Semester ' . $semester, [
            'siswa' => $siswa,
            'nomorSemester' => (int) $semester,
            'rapor' => $rapor,
            'nilai' => $rapor === null ? [] : $model->nilai((string) $rapor['id']),
            'pendukung' => $rapor === null ? [] : $model->dataPendukung((string) $rapor['id']),
            'pengesahan' => PengesahanRapor::baca($rapor ?? [], $model->pengaturanCetak($rapor['pengaturan_cetak_id'] ?? null)),
        ]);
    }

    public function edit(string $id): string
    {
        $siswa = $this->temukanSiswa($id);

        return $this->formIdentitas($siswa, $siswa);
    }

    public function update(string $id): ResponseInterface
    {
        $siswa = $this->temukanSiswa($id);
        $versi = $this->request->getPost('versi');
        $versi = is_string($versi) ? $versi : '';
        $identitas = [];
        $rules = [];
        $errors = [];

        foreach (SiswaModel::IDENTITAS as $fields) {
            foreach ($fields as $field => [$label, $type]) {
                $raw = $this->request->getPost($field);
                $identitas[$field] = is_string($raw) ? trim($raw) : null;
                if (! is_string($raw)) {
                    $errors[$field] = "Isian {$label} tidak valid.";
                }

                $rule = match ($type) {
                    'date' => 'permit_empty|regex_match[/^[1-9]\d{3}-\d{2}-\d{2}$/D]|valid_date[Y-m-d]',
                    'number' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[2147483647]',
                    'textarea' => 'permit_empty|max_length[16000]',
                    default => $field === 'nama' ? 'required|max_length[255]' : 'permit_empty|max_length[255]',
                };
                $rules[$field] = ['label' => $label, 'rules' => $rule];
            }
        }

        $validation = service('validation')->setRules($rules);
        if (! $validation->run($identitas) || $errors !== []) {
            return $this->response->setStatusCode(422)->setBody(
                $this->formIdentitas($siswa, $identitas, $errors + $validation->getErrors(), $versi)
            );
        }

        $identitas = array_map(static fn (?string $value) => $value === '' ? null : $value, $identitas);
        try {
            model(RiwayatArsipModel::class)->catat($id, RiwayatArsipModel::IDENTITAS, (int) session()->get('operator_id'),
                'identitas', function (array $sebelum) use ($id, $versi, $identitas): void {
                    if (! hash_equals(SiswaModel::versiIdentitas($sebelum), $versi)) {
                        throw new DomainException('Identitas sudah berubah. Buka ulang Edit Identitas untuk melihat data terbaru.');
                    }
                    $perubahan = array_filter($identitas, static fn ($nilai, $kolom): bool =>
                        ($nilai === null ? null : (string) $nilai)
                        !== ($sebelum[$kolom] === null ? null : (string) $sebelum[$kolom]), ARRAY_FILTER_USE_BOTH);
                    if ($perubahan !== [] && ! model(SiswaModel::class)->simpanIdentitas($id, $perubahan)) {
                        throw new RuntimeException('Identitas gagal disimpan.');
                    }
                }
            );
        } catch (DomainException $e) {
            return $this->response->setStatusCode(409)->setBody(
                $this->formIdentitas($siswa, $identitas, ['form' => $e->getMessage()], $versi)
            );
        } catch (RuntimeException) {
            log_message('error', 'Gagal menyimpan identitas dan riwayat siswa {id}.', ['id' => $id]);

            return $this->response->setStatusCode(500)->setBody(
                $this->formIdentitas($siswa, $identitas, ['form' => 'Identitas gagal disimpan. Silakan coba lagi.'], $versi)
            );
        }

        return redirect()->route('detail-siswa', [$id], 303)->with('pesan_siswa', 'Identitas siswa tersimpan.')->withCookies();
    }

    private function validasiSemester(string $semester): void
    {
        if (! in_array($semester, ['1', '2', '3', '4', '5', '6'], true)) {
            throw PageNotFoundException::forPageNotFound('Semester tidak ditemukan.');
        }
    }

    private function pdf(string $nama, string $isi): ResponseInterface
    {
        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Cache-Control', 'private, no-store')
            ->setHeader('Content-Disposition', 'inline; filename="RMD-' . $nama . '.pdf"')
            ->setBody($isi);
    }

    private function filterPencarian(): array
    {
        $kata = $this->request->getGet('q');
        $angkatan = $this->request->getGet('angkatan');

        return [
            is_string($kata) ? mb_substr(trim($kata), 0, 100) : '',
            is_string($angkatan) && preg_match('/^[0-9]{4}$/D', $angkatan) ? $angkatan : '',
        ];
    }

    private function temukanSiswa(string $id): array
    {
        $siswa = model(SiswaModel::class)->identitas($id);
        if ($siswa === null) {
            throw PageNotFoundException::forPageNotFound('Siswa tidak ditemukan.');
        }

        return $siswa;
    }

    private function formIdentitas(array $siswa, array $identitas, array $errors = [], ?string $versi = null): string
    {
        return $this->halaman('siswa/edit', 'Edit Identitas', [
            'siswa' => $siswa,
            'identitas' => $identitas,
            'errors' => $errors,
            'versi' => $versi ?? SiswaModel::versiIdentitas($siswa),
        ]);
    }

    private function halaman(string $path, string $title, array $data): string
    {
        return view('operator/' . $path, $data + [
            'pageTitle' => $title,
            'activeMenu' => 'data-siswa',
        ]);
    }
}
