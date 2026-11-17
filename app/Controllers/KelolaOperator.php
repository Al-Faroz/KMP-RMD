<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;

final class KelolaOperator extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        $daftar = model(OperatorModel::class)->select('id, nama_lengkap, username')
            ->orderBy('nama_lengkap')->orderBy('id')->findAll();

        return view('operator/akun/index', [
            'pageTitle' => 'Operator',
            'activeMenu' => 'daftar-operator',
            'daftar' => $daftar,
            'idAktif' => (int) session()->get('operator_id'),
        ]);
    }

    public function tambah(): ResponseInterface
    {
        return $this->form(['nama_lengkap' => '', 'username' => '']);
    }

    public function edit(int $id): ResponseInterface
    {
        return $this->form($this->cariAkun($id));
    }

    public function simpan(?int $id = null): ResponseInterface
    {
        $akun = $id === null ? [] : $this->cariAkun($id);
        $nama = $this->request->getPost('nama_lengkap');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $akun['nama_lengkap'] = is_string($nama) ? trim($nama) : '';
        $akun['username'] = is_string($username) ? trim($username) : '';
        $model = model(OperatorModel::class);

        try {
            if (! is_string($password)) {
                throw new DomainException('Isian kata sandi tidak valid.');
            }
            if ($id === null) {
                $model->buatAkun($akun['nama_lengkap'], $akun['username'], $password);
            } else {
                $model->ubahAkun($id, $akun['nama_lengkap'], $akun['username'], $password);
            }
        } catch (DomainException $e) {
            return $this->form($akun, $e->getMessage());
        } catch (DatabaseException $e) {
            $pesan = (int) $e->getCode() === 1062 ? 'Username sudah dipakai.' : 'Akun gagal disimpan. Coba lagi.';

            return $this->form($akun, $pesan);
        }

        if ($id === (int) session()->get('operator_id') && $password !== '') {
            session()->remove(['operator_id', 'operator_nama', 'operator_kunci']);

            return redirect()->route('masuk', [], 303)->with('sukses', 'Kata sandi diubah. Silakan masuk kembali.');
        }

        return redirect()->route('daftar-operator', [], 303)
            ->with('sukses', $id === null ? 'Operator ditambahkan.' : 'Perubahan operator disimpan.');
    }

    public function konfirmasiHapus(int $id): ResponseInterface
    {
        $akun = $this->cariAkun($id);
        if ($id === (int) session()->get('operator_id')) {
            return redirect()->route('daftar-operator')->with('galat', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        return $this->response->setBody(view('operator/akun/hapus', [
            'pageTitle' => 'Hapus Operator',
            'activeMenu' => 'daftar-operator',
            'akun' => $akun,
        ]));
    }

    public function hapus(int $id): ResponseInterface
    {
        $this->cariAkun($id);

        try {
            model(OperatorModel::class)->hapusAkun($id, (int) session()->get('operator_id'));
        } catch (DomainException $e) {
            return redirect()->route('daftar-operator', [], 303)->with('galat', $e->getMessage());
        } catch (DatabaseException) {
            return redirect()->route('daftar-operator', [], 303)->with('galat', 'Akun gagal dihapus. Coba lagi.');
        }

        return redirect()->route('daftar-operator', [], 303)->with('sukses', 'Operator dihapus.');
    }

    private function form(array $akun, string $galat = ''): ResponseInterface
    {
        return $this->response->setStatusCode($galat === '' ? 200 : 422)->setBody(view('operator/akun/form', [
            'pageTitle' => isset($akun['id']) ? 'Edit Operator' : 'Tambah Operator',
            'activeMenu' => 'daftar-operator',
            'akun' => $akun,
            'galat' => $galat,
        ]));
    }

    private function cariAkun(int $id): array
    {
        $akun = model(OperatorModel::class)->select('id, nama_lengkap, username')->find($id);

        return $akun ?? throw PageNotFoundException::forPageNotFound('Akun operator tidak ditemukan.');
    }
}
