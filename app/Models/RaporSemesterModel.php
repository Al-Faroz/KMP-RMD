<?php

namespace App\Models;

use CodeIgniter\Model;

final class RaporSemesterModel extends Model
{
    protected $table = 'rapor_semester';

    public function riwayat(string $siswaId): array
    {
        $rapor = $this->select('semester, kelas, rombel, tahun_pelajaran, status_arsip')
            ->where('siswa_id', $siswaId)->findAll();

        return array_column($rapor, null, 'semester');
    }

    public function detail(string $siswaId, int $semester): ?array
    {
        return $this->select('rapor_semester.*, format_cetak.nama_format')
            ->join('format_cetak', 'format_cetak.id = rapor_semester.format_cetak_id', 'left')
            ->where('rapor_semester.siswa_id', $siswaId)
            ->where('rapor_semester.semester', $semester)->first();
    }

    public function pengaturanCetak(int|string|null $id): ?array
    {
        if ($id === null) {
            return null;
        }

        return $this->db->table('pengaturan_cetak')->where('id', $id)->get()->getRowArray();
    }

    public function pengaturanIdentitas(string $siswaId): ?array
    {
        return $this->db->table('rapor_semester r')->select('p.*')
            ->join('pengaturan_cetak p', 'p.id = r.pengaturan_cetak_id')
            ->where('r.siswa_id', $siswaId)
            ->where('r.status_arsip', 'Ditemukan')
            ->orderBy('r.semester')
            ->get(1)->getRowArray();
    }

    public function nilai(string $raporId): array
    {
        return $this->db->table('nilai_mapel')->where('rapor_semester_id', $raporId)
            ->orderBy('urutan_mapel')->orderBy('id')->get()->getResultArray();
    }

    public function arsipTranskrip(string $siswaId): array
    {
        return $this->db->table('rapor_semester r')
            ->select('r.semester, f.id_sumber AS kode_format, n.nama_mapel_asli, n.nama_mapel_transkrip, '
                . 'n.nilai_pengetahuan, n.predikat_pengetahuan, n.nilai_keterampilan, n.predikat_keterampilan, n.nilai_akhir, '
                . 'p.tempat_terbit, p.tanggal_terbit, p.nama_kepala_madrasah, p.nip_kepala_madrasah')
            ->join('nilai_mapel n', 'n.rapor_semester_id = r.id', 'left')
            ->join('format_cetak f', 'f.id = r.format_cetak_id', 'left')
            ->join('pengaturan_cetak p', 'p.id = r.pengaturan_cetak_id', 'left')
            ->where('r.siswa_id', $siswaId)
            ->where('r.status_arsip', 'Ditemukan')
            ->whereIn('r.semester', range(1, 6))
            ->orderBy('r.semester')->orderBy('n.urutan_mapel')->orderBy('n.id')
            ->get()->getResultArray();
    }

    public function dataPendukung(string $raporId): array
    {
        return $this->db->table('data_pendukung')->where('rapor_semester_id', $raporId)
            ->orderBy('jenis_item')->orderBy('urutan')->orderBy('id')->get()->getResultArray();
    }
}
