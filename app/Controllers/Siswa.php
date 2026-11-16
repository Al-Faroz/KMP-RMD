<?php

namespace App\Controllers;

use App\Models\RaporSemesterModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

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
        return $this->halaman('siswa/detail', 'Detail Siswa', [
            'siswa' => $this->temukanSiswa($id),
            'riwayat' => model(RaporSemesterModel::class)->riwayat($id),
        ]);
    }

    public function semester(string $id, string $semester): string
    {
        if (! in_array($semester, ['1', '2', '3', '4', '5', '6'], true)) {
            throw PageNotFoundException::forPageNotFound('Semester tidak ditemukan.');
        }

        $siswa = $this->temukanSiswa($id);
        $model = model(RaporSemesterModel::class);
        $rapor = $model->detail($id, (int) $semester);

        return $this->halaman('siswa/semester', 'Semester ' . $semester, [
            'siswa' => $siswa,
            'nomorSemester' => (int) $semester,
            'rapor' => $rapor,
            'nilai' => $rapor === null ? [] : $model->nilai((string) $rapor['id']),
            'pendukung' => $rapor === null ? [] : $model->dataPendukung((string) $rapor['id']),
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
                $this->formIdentitas($siswa, $identitas, $errors + $validation->getErrors())
            );
        }

        $identitas = array_map(static fn (?string $value) => $value === '' ? null : $value, $identitas);
        if (! model(SiswaModel::class)->simpanIdentitas($id, $identitas)) {
            return $this->response->setStatusCode(500)->setBody(
                $this->formIdentitas($siswa, $identitas, ['form' => 'Identitas gagal disimpan. Silakan coba lagi.'])
            );
        }

        return redirect()->route('detail-siswa', [$id], 303)->with('pesan_siswa', 'Identitas siswa tersimpan.')->withCookies();
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

    private function formIdentitas(array $siswa, array $identitas, array $errors = []): string
    {
        return $this->halaman('siswa/edit', 'Edit Identitas', [
            'siswa' => $siswa,
            'identitas' => $identitas,
            'errors' => $errors,
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
