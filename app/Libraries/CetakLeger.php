<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

final class CetakLeger
{
    private const MAKS_KOLOM_NILAI = 22;
    private const SUBKOLOM = [
        'nilai_pengetahuan' => 'P',
        'nilai_keterampilan' => 'K',
        'nilai_akhir' => 'Akhir',
        'rata_rata_ard' => 'Rata ARD',
    ];

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
            $mapel[$kunci] ??= ['nama' => $nama, 'alias' => '', 'urutan' => $urutan, 'bidang' => []];
            $mapel[$kunci]['urutan'] = min($mapel[$kunci]['urutan'], $urutan);
            if ($mapel[$kunci]['alias'] === '' && $alias !== '') {
                $mapel[$kunci]['alias'] = $alias;
            }

            $angka = array_intersect_key($item, self::SUBKOLOM);
            if ($angka['nilai_pengetahuan'] !== null || $angka['nilai_keterampilan'] !== null) {
                $mapel[$kunci]['bidang']['nilai_pengetahuan'] = true;
                $mapel[$kunci]['bidang']['nilai_keterampilan'] = true;
                $angka['rata_rata_ard'] = null;
            }
            foreach (['nilai_akhir', 'rata_rata_ard'] as $field) {
                if ($angka[$field] !== null) {
                    $mapel[$kunci]['bidang'][$field] = true;
                }
            }

            $nilaiSiswa[$id][$kunci] = $angka;
        }

        uasort($mapel, static fn (array $a, array $b): int => [$a['urutan'], $a['nama']] <=> [$b['urutan'], $b['nama']]);
        foreach ($mapel as $kunci => &$item) {
            $item['label'] = self::SINGKATAN[$kunci] ?? ($item['alias'] !== '' ? $item['alias'] : $item['nama']);
            $item['kolom'] = array_intersect_key(self::SUBKOLOM, $item['bidang']);
        }
        unset($item);

        $periode = array_unique(array_filter(array_column($anggota, 'tahun_pelajaran')));
        sort($periode);
        $arsip = array_filter($anggota, static fn (array $item): bool => $item['status_arsip'] === 'Ditemukan');
        $satu = static function (array $baris, string $field): ?string {
            $nilai = array_unique(array_filter(array_column($baris, $field)));
            return count($nilai) === 1 ? (string) reset($nilai) : null;
        };
        $adaTotalAkhir = array_filter(array_column($arsip, 'total_nilai_akhir'), static fn ($nilai): bool => $nilai !== null) !== [];
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
        $bagian = $this->bagiKolom($mapel);
        $isi = [];
        foreach ($bagian as $i => $kolom) {
            $isi[] = [
                'angkatan' => $angkatan, 'semester' => $semester, 'rombel' => $rombel,
                'tahunPelajaran' => implode(', ', $periode),
                'periodeCampuran' => count($periode) > 1,
                'anggota' => $anggota, 'nilaiSiswa' => $nilaiSiswa,
                'mapel' => $kolom, 'adaTotalAkhir' => $adaTotalAkhir, 'batas' => $batas,
                'bagian' => $i + 1, 'jumlahBagian' => count($bagian),
                'pengesahan' => $pengesahan,
            ];
        }

        return (new PdfRmd())->gabungkan('leger', "Leger {$rombel} Semester {$semester}", $isi, true);
    }

    private function bagiKolom(array $mapel): array
    {
        if ($mapel === []) {
            return [[]];
        }

        $lebar = array_values(array_map(static fn (array $item): int => max(1, count($item['kolom'])), $mapel));
        $jumlahMapel = count($lebar);
        $skor = [$jumlahMapel => [0, 0]];
        $batas = [];
        // Minimize the number of parts first, then the sum of squared subject counts to balance their sizes.
        for ($awal = $jumlahMapel - 1; $awal >= 0; $awal--) {
            $jumlahKolom = 0;
            $terbaik = [PHP_INT_MAX, PHP_INT_MAX];
            for ($akhir = $awal; $akhir < $jumlahMapel; $akhir++) {
                $jumlahKolom += $lebar[$akhir];
                if ($jumlahKolom > self::MAKS_KOLOM_NILAI) {
                    break;
                }

                $panjang = $akhir - $awal + 1;
                [$sisaBagian, $sisaSkor] = $skor[$akhir + 1];
                $calon = [$sisaBagian + 1, $sisaSkor + $panjang * $panjang];
                if ($calon <= $terbaik) {
                    $terbaik = $calon;
                    $batas[$awal] = $akhir + 1;
                }
            }
            $skor[$awal] = $terbaik;
        }

        $bagian = [];
        for ($awal = 0; $awal < $jumlahMapel; $awal = $batas[$awal]) {
            $bagian[] = array_slice($mapel, $awal, $batas[$awal] - $awal, true);
        }

        return $bagian;
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
