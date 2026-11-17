<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class CetakDokumenSiswa
{
    private const KOLOM_NILAI = [
        'sks' => 'SKS',
        'beban_jp' => 'Beban JP',
        'jp' => 'JP',
        'nilai_pengetahuan' => 'Pengetahuan',
        'nilai_keterampilan' => 'Keterampilan',
        'rata_rata_ard' => 'Rata-rata ARD',
        'nilai_akhir' => 'Nilai Akhir',
        'j_px_ni' => 'JP × Nilai',
    ];

    private const KOLOM_PREDIKAT = [
        'nilai_pengetahuan' => 'predikat_pengetahuan',
        'nilai_keterampilan' => 'predikat_keterampilan',
    ];

    public function sampul(array $siswa): string
    {
        return (new PdfRmd())->buat('sampul', 'Sampul Laporan Hasil Belajar', ['siswa' => $siswa]);
    }

    public function identitas(array $siswa, ?array $pengaturan): string
    {
        return (new PdfRmd())->buat('identitas', 'Identitas Peserta Didik', [
            'siswa' => $siswa,
            'lahir' => implode(', ', array_filter([
                $siswa['tempat_lahir'], $this->tanggal($siswa['tanggal_lahir']),
            ], static fn ($data) => $data !== null && $data !== '')),
            'diterima' => $this->tanggal($siswa['tanggal_diterima']),
            'pengesahan' => $this->pengesahan($pengaturan),
        ]);
    }

    public function nilai(array $siswa, array $rapor, array $nilai, ?array $pengaturan): string
    {
        $kolom = array_filter(self::KOLOM_NILAI, static function (string $field) use ($nilai): bool {
            $predikat = self::KOLOM_PREDIKAT[$field] ?? null;
            foreach ($nilai as $mapel) {
                if ($mapel[$field] !== null || ($predikat !== null && $mapel[$predikat] !== null)) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);
        $batasNilai = array_filter([
            'KBM' => $rapor['kbm'] ?? $pengaturan['kbm'] ?? null,
            'KKM' => $rapor['kkm'] ?? $pengaturan['kkm'] ?? null,
            'KKTP' => $rapor['kktp'] ?? $pengaturan['kktp'] ?? null,
        ], static fn ($data) => $data !== null);
        $ringkasan = array_filter([
            'Total SKS' => $rapor['total_sks'],
            'Total beban ARD' => $rapor['total_beban_ard'],
            'Total JP' => $rapor['total_jp'],
            'Total Pengetahuan' => $rapor['total_nilai_pengetahuan'],
            'Total Keterampilan' => $rapor['total_nilai_keterampilan'],
            'Total Nilai Akhir' => $rapor['total_nilai_akhir'],
            'Total JP × Nilai' => $rapor['total_j_px_ni'],
            'IP Semester' => $rapor['ip_semester'],
        ], static fn ($data) => $data !== null);

        return (new PdfRmd())->buat('lembar_nilai', 'Kartu Hasil Studi', [
            'siswa' => $siswa,
            'rapor' => $rapor,
            'nilai' => $nilai,
            'kolom' => $kolom,
            'kolomPredikat' => array_intersect_key(self::KOLOM_PREDIKAT, $kolom),
            'batasNilai' => $batasNilai,
            'ringkasan' => $ringkasan,
            'pengesahan' => PengesahanRapor::baca($rapor, $pengaturan),
        ]);
    }

    private function pengesahan(?array $pengaturan): array
    {
        return [
            'tempat' => $pengaturan['tempat_terbit'] ?? 'Jombang',
            'tanggal' => $this->tanggal($pengaturan['tanggal_terbit'] ?? null),
            'nama' => $pengaturan['nama_kepala_madrasah'] ?? null,
            'nip' => $pengaturan['nip_kepala_madrasah'] ?? null,
        ];
    }

    private function tanggal(?string $tanggal): ?string
    {
        return $tanggal === null ? null : Time::parse($tanggal, 'Asia/Jakarta', 'id_ID')->toLocalizedString('d MMMM y');
    }
}
