<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class CetakRapor
{
    private const FORMAT = [
        'EXCEL_KHS1' => [
            'judul' => 'PENCAPAIAN KOMPETENSI SISWA',
            'nilai' => [
                'SKS' => 'sks',
                'Pengetahuan' => [
                    'Nilai' => 'nilai_pengetahuan', 'SKS × N1' => 'sk_sx_n1',
                    'Predikat' => 'predikat_pengetahuan', 'Deskripsi' => 'deskripsi_pengetahuan',
                ],
                'Keterampilan' => [
                    'Nilai' => 'nilai_keterampilan', 'SKS × N2' => 'sk_sx_n2',
                    'Predikat' => 'predikat_keterampilan', 'Deskripsi' => 'deskripsi_keterampilan',
                ],
            ],
        ],
        'ARD_KHS2' => [
            'judul' => 'CAPAIAN HASIL BELAJAR',
            'nilai' => [
                'Beban / JP' => 'beban_jp',
                'Pengetahuan' => ['Nilai' => 'nilai_pengetahuan', 'Predikat' => 'predikat_pengetahuan'],
                'Keterampilan' => ['Nilai' => 'nilai_keterampilan', 'Predikat' => 'predikat_keterampilan'],
                'Rata-rata' => 'rata_rata_ard', 'Beban × Nilai' => 'bebanx_n',
            ],
        ],
        'RDM_K13' => [
            'judul' => 'CAPAIAN HASIL BELAJAR',
            'nilai' => [
                'JP' => 'jp',
                'Pengetahuan' => ['Nilai' => 'nilai_pengetahuan', 'Predikat' => 'predikat_pengetahuan'],
                'Keterampilan' => ['Nilai' => 'nilai_keterampilan', 'Predikat' => 'predikat_keterampilan'],
                'Rata-rata' => 'rata_rata_ard', 'Beban × Nilai' => 'bebanx_n',
            ],
        ],
        'RDM_PDF_2023_VIIA' => [
            'judul' => 'CAPAIAN HASIL BELAJAR',
            'nilai' => [
                'JP' => 'jp', 'Nilai Akhir' => 'nilai_akhir', 'JP × Nilai' => 'j_px_ni',
                'Capaian Kompetensi' => 'capaian_kompetensi',
            ],
        ],
    ];

    private const RINGKASAN = [
        'Total SKS' => 'total_sks',
        'Total beban ARD' => 'total_beban_ard',
        'Total JP' => 'total_jp',
        'Total Nilai Pengetahuan' => 'total_nilai_pengetahuan',
        'Total Nilai Keterampilan' => 'total_nilai_keterampilan',
        'Total SKS × N1' => 'total_sk_sx_n1',
        'Total SKS × N2' => 'total_sk_sx_n2',
        'Total Beban × Nilai' => 'total_bebanx_n',
        'Total Nilai Akhir' => 'total_nilai_akhir',
        'Total JP × Nilai' => 'total_j_px_ni',
        'IP Semester' => 'ip_semester',
        'SKS Berikutnya' => 'sks_berikutnya',
        'Peringkat' => 'peringkat',
    ];

    public function mendukung(?string $kode): bool
    {
        return isset(self::FORMAT[$kode]);
    }

    public function buat(array $siswa, array $rapor, array $mapel, array $pendukung, ?array $pengaturan, array $rentang): string
    {
        $kode = $rapor['kode_format'];
        $format = self::FORMAT[$kode];
        $kolom = $format['nilai'];
        if ($kode === 'RDM_K13') {
            foreach (['Rata-rata', 'Beban × Nilai'] as $label) {
                if (array_filter(array_column($mapel, $kolom[$label]), static fn ($nilai): bool => $nilai !== null) === []) {
                    unset($kolom[$label]);
                }
            }
        }

        $ringkasan = [];
        foreach (self::RINGKASAN as $label => $field) {
            if ($rapor[$field] !== null) {
                $ringkasan[$label] = $rapor[$field];
            }
        }

        $batas = [];
        foreach (['KBM', 'KKM', 'KKTP'] as $label) {
            $field = strtolower($label);
            $nilai = $rapor[$field] ?? $pengaturan[$field] ?? null;
            if ($nilai !== null) {
                $batas[$label] = $nilai;
            }
        }

        $dataPendukung = [];
        foreach ($pendukung as $item) {
            $dataPendukung[mb_strtolower(trim($item['jenis_item']))][] = $item;
        }

        $tempatTanggal = $pengaturan['tempat_tanggal_cetak_asli'] ?? null;
        if ($tempatTanggal === null || $tempatTanggal === '') {
            $tanggal = $pengaturan['tanggal_terbit'] ?? null;
            $tempat = $pengaturan['tempat_terbit'] ?? null;
            $tempatTanggal = implode(', ', array_filter([
                $tempat,
                $tanggal === null ? null : Time::parse($tanggal, 'Asia/Jakarta', 'id_ID')->toLocalizedString('d MMMM y'),
            ]));
        }

        return (new PdfRmd())->buat('rapor', 'Rapor Semester ' . $rapor['semester'], [
            'siswa' => $siswa,
            'rapor' => $rapor,
            'mapel' => $mapel,
            'judul' => $format['judul'],
            'kolom' => $kolom,
            'ringkasan' => $ringkasan,
            'batas' => $batas,
            'pendukung' => $dataPendukung,
            'rentang' => $rentang,
            'pengaturan' => $pengaturan,
            'tempatTanggal' => $tempatTanggal,
            'merdeka' => $kode === 'RDM_PDF_2023_VIIA',
            'deskripsiTerpisah' => in_array($kode, ['ARD_KHS2', 'RDM_K13'], true),
        ]);
    }
}
