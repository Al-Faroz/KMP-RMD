<?php

namespace App\Libraries;

use PhpOffice\PhpWord\Settings;

final class CetakKhsWord
{
    private const TEMPLATES = ['EXCEL_KHS1' => 'KHS1.docx', 'ARD_KHS2' => 'KHS2.docx'];

    public static function mendukung(?string $kode): bool
    {
        return isset(self::TEMPLATES[$kode]);
    }

    public function simpan(array $data, string $tujuan): void
    {
        $kode = $data['rapor']['kode_format'];
        $khs1 = $kode === 'EXCEL_KHS1';
        Settings::setOutputEscapingEnabled(true);
        $template = new TemplateKhs(APPPATH . 'Assets/rapor/' . self::TEMPLATES[$kode]);
        $template->isiMapel('n', $this->barisMapel($data['mapel'], false, $khs1));
        if (! $khs1) {
            $template->isiMapel('d', $this->barisMapel($data['mapel'], true, false));
        }

        $this->isiPendukung($template, 'ekstra', $data['pendukung']['ekstrakurikuler'] ?? [], ! $khs1);
        $this->isiPendukung($template, 'prestasi', $data['pendukung']['prestasi'] ?? [], false);
        $template->setValues($this->isian($data, $khs1));
        $template->saveAs($tujuan);
    }

    private function isian(array $data, bool $khs1): array
    {
        ['siswa' => $siswa, 'rapor' => $rapor, 'pendukung' => $pendukung, 'pengesahan' => $sah] = $data;
        $sekolah = $data['pengaturan'];
        $rombel = $rapor['rombel'] ?? $rapor['kelas'] ?? '';
        $kelas = $rapor['kelas'] ?? $rombel;
        $field = [
            'nama_siswa' => $siswa['nama'],
            'sekolah' => $sekolah['nama_madrasah'] ?? 'MTsN 4 Jombang',
            'tahun' => $rapor['tahun_pelajaran'] ?? '',
            'spiritual' => $this->catatan($pendukung['sikap spiritual'] ?? []),
            'sosial' => $this->catatan($pendukung['sikap sosial'] ?? []),
            'catatan' => $this->catatan($pendukung['catatan wali kelas'] ?? []),
            'tanggapan' => $this->catatan($pendukung['tanggapan orang tua/wali'] ?? $pendukung['tanggapan orang tua'] ?? []),
            'tanggal' => $sah['tempat_tanggal_cetak_asli'] ?? '',
            'jabatan_wali' => $sah['jabatan_cetak'] ?? '',
            'wali' => $sah['nama_penandatangan'] ?? '',
            'nip_wali' => $this->nip($sah['nip_penandatangan'] ?? ''),
            'jabatan_kepala' => $sah['jabatan_kepala_cetak'] ?? '',
            'kepala' => $sah['nama_kepala_madrasah'] ?? '',
            'nip_kepala' => $this->nip($sah['nip_kepala_madrasah'] ?? ''),
            'ip' => $this->angka($rapor['ip_semester'] ?? null, $khs1 ? 2 : null),
        ];

        foreach ($pendukung['ketidakhadiran'] ?? [] as $item) {
            $label = strtolower(str_replace(' ', '_', trim($item['nama_item'] ?? '')));
            if (in_array($label, ['sakit', 'izin', 'tanpa_keterangan'], true)) {
                $arsip = ltrim(trim($item['isian_tercetak_asli'] ?? ''), ': ');
                $field[$label] = $arsip === '' ? trim($this->angka($item['jumlah'] ?? null) . ' ' . ($item['satuan'] ?? '')) : $arsip;
            }
        }
        $field += ['sakit' => '', 'izin' => '', 'tanpa_keterangan' => ''];

        if ($khs1) {
            $ipk = $this->catatan($pendukung['ipk'] ?? []);

            return $field + [
                'alamat' => $sekolah['alamat_madrasah'] ?? '',
                'nis_nisn' => implode(' / ', array_filter([$siswa['nis'], $siswa['nisn']], static fn ($nomor): bool => $nomor !== null && $nomor !== '')),
                'kelas' => $rombel,
                'semester' => $rapor['semester'],
                'kbm' => $this->angka($data['batas']['KBM'] ?? null),
                'total_sks' => $this->angka($rapor['total_sks'] ?? null),
                'total_n1' => $this->angka($rapor['total_nilai_pengetahuan'] ?? null),
                'total_sks_n1' => $this->angka($rapor['total_sk_sx_n1'] ?? null),
                'total_n2' => $this->angka($rapor['total_nilai_keterampilan'] ?? null),
                'total_sks_n2' => $this->angka($rapor['total_sk_sx_n2'] ?? null),
                'peringkat' => $this->angka($rapor['peringkat'] ?? null),
                'sks_berikutnya' => $this->angka($rapor['sks_berikutnya'] ?? null),
                'ipk' => $ipk === '' ? $this->angka($pendukung['ipk'][0]['nilai'] ?? null, 2) : $ipk,
            ];
        }

        $predikat = array_column($data['rentang'], 'rentang_tercetak', 'predikat');

        return $field + [
            'nis' => $siswa['nis'] ?? '',
            'nisn' => $siswa['nisn'] ?? '',
            'kelas_semester' => ($kelas === '' ? '' : $kelas . '/') . $rapor['semester'],
            'p_spiritual' => $pendukung['sikap spiritual'][0]['predikat'] ?? '',
            'p_sosial' => $pendukung['sikap sosial'][0]['predikat'] ?? '',
            'kkm' => $this->angka($data['batas']['KKM'] ?? null),
            'total_beban' => $this->angka($rapor['total_beban_ard'] ?? null),
            'total_pengetahuan' => $this->angka($rapor['total_nilai_pengetahuan'] ?? null),
            'total_keterampilan' => $this->angka($rapor['total_nilai_keterampilan'] ?? null),
            'total_bxn' => $this->angka($rapor['total_bebanx_n'] ?? null),
            'pred_d' => $predikat['D'] ?? '',
            'pred_c' => $predikat['C'] ?? '',
            'pred_b' => $predikat['B'] ?? '',
            'pred_a' => $predikat['A'] ?? '',
        ];
    }

    private function barisMapel(array $mapel, bool $deskripsi, bool $khs1): array
    {
        $baris = [];
        $kelompok = $subkelompok = null;
        $nomor = 0;
        foreach ($mapel as $item) {
            $namaKelompok = trim($item['kelompok_mapel'] ?? '');
            if ($namaKelompok !== $kelompok) {
                $kelompok = $namaKelompok;
                $subkelompok = null;
                $nomor = 0;
                if ($kelompok !== '') {
                    $baris[] = ['kelompok', ['kelompok' => $kelompok]];
                }
            }
            $namaSub = trim($item['subkelompok_mapel'] ?? '');
            if (! $khs1 && str_ends_with($namaSub, '*)')) {
                $namaSub = rtrim(substr($namaSub, 0, -2)) . ' (bila ada)';
            }
            if ($namaSub !== $subkelompok) {
                $subkelompok = $namaSub;
                if ($subkelompok !== '') {
                    $baris[] = ['subkelompok', ['subkelompok' => $subkelompok, 'subnomor' => ++$nomor]];
                }
            }

            $nilai = [
                'no' => $subkelompok === '' ? ++$nomor : '',
                'mapel' => $item['nama_mapel_asli'],
            ];
            if ($deskripsi || $khs1) {
                $nilai += ['d1' => $item['deskripsi_pengetahuan'] ?? '', 'd2' => $item['deskripsi_keterampilan'] ?? ''];
            }
            if (! $deskripsi) {
                $nilai += [
                    'n1' => $this->angka($item['nilai_pengetahuan'] ?? null),
                    'p1' => $item['predikat_pengetahuan'] ?? '',
                    'n2' => $this->angka($item['nilai_keterampilan'] ?? null),
                    'p2' => $item['predikat_keterampilan'] ?? '',
                ];
                $nilai += $khs1 ? [
                    'sks' => $this->angka($item['sks'] ?? null),
                    'sksn1' => $this->angka($item['sk_sx_n1'] ?? null),
                    'sksn2' => $this->angka($item['sk_sx_n2'] ?? null),
                ] : [
                    'beban' => $this->angka($item['beban_jp'] ?? null),
                    'rata' => $this->angka($item['rata_rata_ard'] ?? null, 0),
                    'bxn' => $this->angka($item['bebanx_n'] ?? null, 0),
                ];
            }
            $baris[] = ['mapel', $nilai];
        }

        return $baris;
    }

    private function isiPendukung(TemplateKhs $template, string $bagian, array $items, bool $predikat): void
    {
        $nilai = [];
        foreach (array_pad($items, 3, []) as $i => $item) {
            $baris = [
                $bagian . '_no' => (string) ($i + 1),
                $bagian . '_nama' => $item['nama_item'] ?? '',
                $bagian . '_deskripsi' => $this->catatan([$item]),
            ];
            if ($predikat) {
                $baris[$bagian . '_predikat'] = $item['predikat'] ?? $this->angka($item['nilai'] ?? null);
            }
            $nilai[] = $baris;
        }
        $template->cloneRowAndSetValues($bagian . '_nama', $nilai);
    }

    private function catatan(array $items): string
    {
        return implode("\n", array_filter(
            array_map(static fn (array $item): string => trim($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? ''), $items),
            static fn (string $teks): bool => $teks !== ''
        ));
    }

    private function angka(int|float|string|null $nilai, ?int $desimal = null): string
    {
        if ($nilai === null || $nilai === '') {
            return '';
        }

        return $desimal === null
            ? str_replace('.', ',', RaporFields::number((string) $nilai))
            : number_format((float) $nilai, $desimal, ',', '');
    }

    private function nip(string $nip): string
    {
        return $nip === '' || str_starts_with(strtoupper($nip), 'NIP') ? $nip : 'NIP. ' . $nip;
    }
}
