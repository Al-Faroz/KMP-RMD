<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class CetakLeger
{
    private const MAPEL_PER_BAGIAN = 18;

    private const SINGKATAN = [
        'alquranhadis' => 'QH',
        'akidahakhlak' => 'AA',
        'fikih' => 'FIK',
        'sejarahkebudayaanislam' => 'SKI',
        'bahasaarab' => 'BAR',
        'pendidikanpancasila' => 'PP',
        'pendidikanpancasiladankewarganegaraan' => 'PPKn',
        'bahasaindonesia' => 'BINDO',
        'matematika' => 'MTK',
        'ilmupengetahuanalam' => 'IPA',
        'ilmupengetahuansosial' => 'IPS',
        'bahasainggris' => 'BING',
        'pendidikanjasmaniolahragadankesehatan' => 'PJOK',
        'informatika' => 'INFO',
        'senibudaya' => 'SBK',
        'senibudayaprakarya' => 'SBP',
        'prakarya' => 'Prakarya',
        'bahasadaerah' => 'BADER',
        'bahasajawa' => 'B. Jawa',
        'aswaja' => 'ASW',
        'mabadifiqhiyyah' => 'MBD',
    ];

    public function buat(int $angkatan, int $semester, string $rombel, array $anggota, array $nilai, ?array $pengaturan): string
    {
        $siswa = array_column($anggota, null, 'id');
        $mapel = $nilaiSiswa = [];
        foreach ($nilai as $item) {
            $id = $item['rapor_semester_id'];
            $nama = $item['nama_mapel_asli'];
            $kunci = $kunciAsli = $this->kunci($nama);
            // Keep separate archive rows when two subjects normalize to the same name.
            for ($nomor = 2; isset($nilaiSiswa[$id][$kunci]); $nomor++) {
                $kunci = $kunciAsli . '#' . $nomor;
            }

            $urutan = $item['urutan_mapel'] ?? PHP_INT_MAX;
            $alias = trim((string) $item['nama_mapel_leger']);
            $mapel[$kunci] ??= ['nama' => $nama, 'alias' => '', 'urutan' => $urutan];
            $mapel[$kunci]['urutan'] = min($mapel[$kunci]['urutan'], $urutan);
            if ($mapel[$kunci]['alias'] === '' && $alias !== '') {
                $mapel[$kunci]['alias'] = $alias;
            }

            $nilaiSiswa[$id][$kunci] = $this->nilaiCetak($siswa[$id]['kode_format'], $item);
        }

        uasort($mapel, static fn (array $a, array $b): int => [$a['urutan'], $a['nama']] <=> [$b['urutan'], $b['nama']]);
        foreach ($mapel as $kunci => &$item) {
            $item['label'] = self::SINGKATAN[$kunci] ?? ($item['alias'] !== '' ? $item['alias'] : $item['nama']);
        }
        unset($item);

        $periode = array_unique(array_filter(array_column($anggota, 'tahun_pelajaran')));
        sort($periode);
        $arsip = array_filter($anggota, static fn (array $item): bool => $item['status_arsip'] === 'Ditemukan');
        $satu = static function (array $baris, string $field): ?string {
            $nilai = array_unique(array_filter(array_column($baris, $field)));
            return count($nilai) === 1 ? (string) reset($nilai) : null;
        };
        $ringkasan = array_filter([
            'Total nilai akhir' => 'total_nilai_akhir',
            'IP Semester' => 'ip_semester',
        ], static fn (string $field): bool => array_filter(array_column($arsip, $field), static fn ($nilai): bool => $nilai !== null) !== []);
        $batas = [];
        foreach (['KBM', 'KKM', 'KKTP'] as $label) {
            $field = strtolower($label);
            $angka = array_unique(array_filter(array_column($arsip, $field), static fn ($nilai): bool => $nilai !== null));
            if ($angka === [] && isset($pengaturan[$field])) {
                $angka[] = $pengaturan[$field];
            }
            if (count($angka) === 1) {
                $batas[$label] = $this->angka(reset($angka));
            }
        }

        $tanggal = $pengaturan['tanggal_terbit'] ?? null;
        $penandatangan = $satu($arsip, 'nama_penandatangan');
        $pengesahan = [
            'tanggal' => $tanggal === null ? null : Time::parse($tanggal, 'Asia/Jakarta', 'id_ID')->toLocalizedString('d MMMM y'),
            'tempat' => $pengaturan['tempat_terbit'] ?? null,
            'jabatan' => $satu($arsip, 'jabatan_cetak'),
            'nama' => $penandatangan,
            'nip' => $penandatangan === null ? null : $satu($arsip, 'nip_penandatangan'),
            'kepala' => $pengaturan['nama_kepala_madrasah'] ?? null,
            'nipKepala' => $pengaturan['nip_kepala_madrasah'] ?? null,
        ];
        $bagian = array_chunk($mapel, self::MAPEL_PER_BAGIAN, true) ?: [[]];
        $isi = [];
        foreach ($bagian as $i => $kolom) {
            $isi[] = [
                'angkatan' => $angkatan, 'semester' => $semester, 'rombel' => $rombel,
                'tahunPelajaran' => implode(', ', $periode),
                'periodeCampuran' => count($periode) > 1,
                'anggota' => $anggota, 'nilaiSiswa' => $nilaiSiswa,
                'mapel' => $kolom, 'ringkasan' => $ringkasan, 'batas' => $batas,
                'bagian' => $i + 1, 'jumlahBagian' => count($bagian),
                'pengesahan' => $pengesahan,
            ];
        }

        return (new PdfRmd())->gabungkan('leger', "Leger {$rombel} Semester {$semester}", $isi, true);
    }

    private function nilaiCetak(?string $kode, array $item): string
    {
        if ($item['nilai_akhir'] !== null && ($kode === 'RDM_PDF_2023_VIIA' || $kode === null)) {
            return $this->angka($item['nilai_akhir']);
        }
        if ($kode === 'ARD_KHS2' && $item['rata_rata_ard'] !== null) {
            return $this->angka($item['rata_rata_ard']);
        }
        if ($item['nilai_pengetahuan'] === null && $item['nilai_keterampilan'] === null) {
            return $item['nilai_akhir'] === null ? '—' : $this->angka($item['nilai_akhir']);
        }

        $pengetahuan = $item['nilai_pengetahuan'] === null ? '—' : $this->angka($item['nilai_pengetahuan']);
        $keterampilan = $item['nilai_keterampilan'] === null ? '—' : $this->angka($item['nilai_keterampilan']);

        return $pengetahuan . '/' . $keterampilan;
    }

    private function angka(int|float|string $nilai): string
    {
        $text = (string) $nilai;

        return str_contains($text, '.') ? str_replace('.', ',', rtrim(rtrim($text, '0'), '.')) : $text;
    }

    private function kunci(string $nama): string
    {
        $nama = preg_replace('/^[a-z]\.\s+/iu', '', trim($nama));
        $nama = str_replace('&', ' dan ', mb_strtolower($nama));
        $kunci = preg_replace('/[^\p{L}\p{N}]+/u', '', $nama);

        return match ($kunci) {
            'alquranhadits', 'quranhadis', 'quranhadits' => 'alquranhadis',
            'fiqih', 'fiqh' => 'fikih',
            default => $kunci,
        };
    }
}
