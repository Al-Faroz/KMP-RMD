<?php

namespace App\Controllers;

use App\Libraries\PerbandinganArsip;
use App\Models\RiwayatArsipModel;
use App\Models\SiswaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use RuntimeException;

final class RiwayatArsip extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(string $id, string $semester = '0'): string
    {
        if (! in_array($semester, ['0', '1', '2', '3', '4', '5', '6'], true)) {
            throw PageNotFoundException::forPageNotFound('Semester tidak ditemukan.');
        }
        $siswa = $this->siswa($id);
        $model = model(RiwayatArsipModel::class);
        $daftar = $model->daftar($id, (int) $semester);

        return $this->halaman('index', $siswa, (int) $semester, [
            'pageTitle' => $semester === '0' ? 'Riwayat Identitas' : 'Riwayat Rapor Semester ' . $semester,
            'daftar' => $daftar,
            'pager' => $model->pager,
        ]);
    }

    public function detail(string $id, string $riwayatId): string
    {
        $siswa = $this->siswa($id);
        $riwayat = $this->riwayat($id, $riwayatId);
        $semester = (int) $riwayat['semester'];
        $terakhir = model(RiwayatArsipModel::class)->terakhir($id, $semester);

        return $this->halaman('detail', $siswa, $semester, [
            'pageTitle' => 'Detail Perubahan',
            'riwayat' => $riwayat,
            'perubahan' => (new PerbandinganArsip())->baca($riwayat),
            'bolehPulihkan' => $riwayatId === $terakhir,
        ]);
    }

    public function pulihkan(string $id, string $riwayatId): ResponseInterface
    {
        $riwayat = $this->riwayat($id, $riwayatId);
        try {
            model(RiwayatArsipModel::class)->pulihkan($id, $riwayat, (int) session()->get('operator_id'));
        } catch (DomainException $e) {
            return redirect()->route('detail-riwayat', [$id, $riwayatId], 303)->with('galat', $e->getMessage())->withCookies();
        } catch (RuntimeException) {
            log_message('error', 'Gagal memulihkan riwayat arsip {id}.', ['id' => $riwayatId]);

            return redirect()->route('detail-riwayat', [$id, $riwayatId], 303)
                ->with('galat', 'Pemulihan gagal. Silakan coba lagi.')->withCookies();
        }

        $semester = (int) $riwayat['semester'];
        $identitas = $semester === RiwayatArsipModel::IDENTITAS;

        return redirect()->route($identitas ? 'riwayat-identitas' : 'riwayat-rapor', $identitas ? [$id] : [$id, $semester], 303)
            ->with('sukses', 'Versi sebelumnya dipulihkan.')->withCookies();
    }

    private function siswa(string $id): array
    {
        return model(SiswaModel::class)->identitas($id)
            ?? throw PageNotFoundException::forPageNotFound('Siswa tidak ditemukan.');
    }

    private function riwayat(string $id, string $riwayatId): array
    {
        return model(RiwayatArsipModel::class)->detail($id, $riwayatId)
            ?? throw PageNotFoundException::forPageNotFound('Riwayat perubahan tidak ditemukan.');
    }

    private function halaman(string $file, array $siswa, int $semester, array $data): string
    {
        return view('operator/riwayat/' . $file, $data + [
            'activeMenu' => 'data-siswa',
            'siswa' => $siswa,
            'semester' => $semester,
        ]);
    }
}
