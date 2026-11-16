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

    public function nilai(string $raporId): array
    {
        return $this->db->table('nilai_mapel')->where('rapor_semester_id', $raporId)
            ->orderBy('urutan_mapel')->orderBy('id')->get()->getResultArray();
    }

    public function dataPendukung(string $raporId): array
    {
        return $this->db->table('data_pendukung')->where('rapor_semester_id', $raporId)
            ->orderBy('jenis_item')->orderBy('urutan')->orderBy('id')->get()->getResultArray();
    }
}
