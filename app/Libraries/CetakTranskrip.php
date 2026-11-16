<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class CetakTranskrip
{
    public function buat(array $siswa, array $arsip): string
    {
        return (new PdfRmd())->buat('transkrip', 'Transkrip Nilai Enam Semester',
            $this->susun($arsip) + ['siswa' => $siswa]);
    }

    private function susun(array $arsip): array
    {
        $padanan = [];
        foreach ($arsip as $item) {
            if (trim((string) $item['nama_mapel_transkrip']) !== '') {
                $padanan[$this->kunciMapel($item['nama_mapel_asli'])][trim($item['nama_mapel_transkrip'])] = true;
            }
        }

        $semester = [];
        $merdeka = [];
        $mapel = [];
        $pengesahan = [];
        foreach ($arsip as $item) {
            $nomor = (int) $item['semester'];
            $semester[$nomor] = true;
            $nilaiAkhir = $item['kode_format'] === 'RDM_PDF_2023_VIIA'
                || ($item['kode_format'] === null && $item['nilai_akhir'] !== null);
            if ($nilaiAkhir) {
                $merdeka[$nomor] = true;
            }
            if ($item['nama_kepala_madrasah'] !== null || $item['tanggal_terbit'] !== null) {
                $pengesahan = $item;
            }
            if ($item['nama_mapel_asli'] === null) {
                continue;
            }

            $alternatif = array_keys($padanan[$this->kunciMapel($item['nama_mapel_asli'])] ?? []);
            $nama = trim((string) $item['nama_mapel_transkrip']);
            if ($nama === '') {
                $nama = count($alternatif) === 1 ? $alternatif[0] : $item['nama_mapel_asli'];
            }
            $kunci = $this->kunciMapel($nama);
            // Preserve distinct source rows when a display alias would put two grades in the same cell.
            if (isset($mapel[$kunci]['semester'][$nomor])) {
                $kunci .= '|' . $this->kunciMapel($item['nama_mapel_asli']);
                $nama = $item['nama_mapel_asli'];
            }
            $asal = $kunci;
            for ($i = 2; isset($mapel[$kunci]['semester'][$nomor]); $i++) {
                $kunci = $asal . '|' . $i;
            }

            $mapel[$kunci]['nama'] ??= $nama;
            $mapel[$kunci]['semester'][$nomor] = [
                'pengetahuan' => [
                    'angka' => $nilaiAkhir ? $item['nilai_akhir'] : $item['nilai_pengetahuan'],
                    'huruf' => $nilaiAkhir ? null : $item['predikat_pengetahuan'],
                ],
                'keterampilan' => [
                    'angka' => $nilaiAkhir ? null : $item['nilai_keterampilan'],
                    'huruf' => $nilaiAkhir ? null : $item['predikat_keterampilan'],
                ],
            ];
        }

        foreach ($mapel as &$baris) {
            foreach (['pengetahuan', 'keterampilan'] as $bagian) {
                $angka = array_filter(array_column(array_column($baris['semester'], $bagian), 'angka'),
                    static fn ($nilai): bool => $nilai !== null);
                $baris['rata'][$bagian] = $angka === [] ? '' : number_format(array_sum($angka) / count($angka), 2, ',', '');
            }
        }
        unset($baris);

        $tanggal = $pengesahan['tanggal_terbit'] ?? null;
        $tempat = trim((string) ($pengesahan['tempat_terbit'] ?? '')) ?: 'Jombang';
        return [
            'mapel' => array_values($mapel),
            'judulPengetahuan' => match (count($merdeka)) {
                0 => 'A. Nilai Pengetahuan',
                count($semester) => 'A. Nilai Akhir',
                default => 'A. Nilai Pengetahuan dan Nilai Akhir',
            },
            'semesterMerdeka' => array_keys($merdeka),
            'semesterBelumDitemukan' => array_diff(range(1, 6), array_keys($semester)),
            'pengesahan' => [
                'tempatTanggal' => $tempat . ', ' . ($tanggal === null
                    ? '........................' : Time::parse($tanggal, 'Asia/Jakarta', 'id_ID')->toLocalizedString('d MMMM y')),
                'nama' => $pengesahan['nama_kepala_madrasah'] ?? '........................................',
                'nip' => $pengesahan['nip_kepala_madrasah'] ?? '........................................',
            ],
        ];
    }

    private function kunciMapel(string $nama): string
    {
        $nama = str_replace(['’', '‘'], "'", $nama);

        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', $nama)));
    }
}
