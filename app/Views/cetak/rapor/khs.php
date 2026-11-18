<?php

use App\Libraries\RaporFields;

$khs1 = $rapor['kode_format'] === 'EXCEL_KHS1';
$khs = true;
$tampil = static function (mixed $nilai, ?string $field = null) use ($khs1): string {
    if ($nilai === null || $nilai === '') {
        return '';
    }
    if (! $khs1 && in_array($field, ['rata_rata_ard', 'bebanx_n'], true)) {
        return number_format((float) $nilai, 0, ',', '');
    }

    $teks = (string) $nilai;

    return preg_match('/^-?\d+\.\d+$/D', $teks) === 1 ? str_replace('.', ',', RaporFields::number($teks)) : $teks;
};
$potongan = static function (?string $teks): array {
    $teks ??= '';
    $hasil = [];
    while (mb_strlen($teks) > 700) {
        $awal = mb_substr($teks, 0, 700);
        $spasi = mb_strrpos($awal, ' ');
        $batas = $spasi !== false && $spasi > 350 ? $spasi : 700;
        $hasil[] = mb_substr($teks, 0, $batas);
        $teks = ltrim(mb_substr($teks, $batas));
    }
    $hasil[] = $teks;

    return $hasil;
};
$teks = static fn (array $item): string => trim($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? '');
$ip = $rapor['ip_semester'] ?? null;
$identitas = $khs1 ? [
    ['Nama Sekolah', $pengaturan['nama_madrasah'] ?? 'MTsN 4 Jombang', 'Kelas', $rapor['rombel'] ?? $rapor['kelas'] ?? ''],
    ['Alamat', $pengaturan['alamat_madrasah'] ?? '', 'Semester', $rapor['semester']],
    ['Nama', $siswa['nama'], 'Tahun Pelajaran', $rapor['tahun_pelajaran'] ?? ''],
    ['No. Induk / NISN', implode(' / ', array_filter([$siswa['nis'], $siswa['nisn']], static fn ($nomor): bool => $nomor !== null && $nomor !== '')), '', ''],
] : [
    ['Nama', $siswa['nama'], 'Madrasah', $pengaturan['nama_madrasah'] ?? 'MTsN 4 Jombang'],
    ['NIS', $siswa['nis'] ?? '', 'Kelas / Semester', ($rapor['kelas'] ?? $rapor['rombel'] ?? '') . ' / ' . $rapor['semester']],
    ['NISN', $siswa['nisn'] ?? '', 'Tahun Pembelajaran', $rapor['tahun_pelajaran'] ?? ''],
];
?>
<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .khs { font-size: 9pt; }
    .khs h1 { font-size: 13pt; margin: 0 0 4mm; }
    .khs h2 { font-size: 10pt; margin: 3mm 0 1.5mm; page-break-after: avoid; }
    .khs h3 { font-size: 9pt; margin: 2.5mm 0 1.1mm; page-break-after: avoid; }
    .khs .identitas-khs2 { border-top: .5pt solid #555; border-bottom: .5pt solid #555; margin-bottom: 3mm; }
    .khs p { margin: 1mm 0 2mm; }
    .khs .rapor-tabel, .khs .rapor-deskripsi { width: 100%; font-size: 8.5pt; text-align: center; }
    .khs td, .khs th { vertical-align: top; }
    .khs .rapor-tabel td, .khs .rapor-tabel th { border: .45pt solid #616161; padding: 1mm .9mm; }
    .khs th { background: #e9e9e9; vertical-align: middle; }
    .khs .teks, .khs .kelompok, .khs .subkelompok { text-align: left; }
    .khs .kelompok, .khs .subkelompok { font-weight: bold; }
    .khs .kelompok { background: #f0f0f0; }
    .khs .judul-kelompok { page-break-after: avoid; }
    .khs .rapor-khs1 { font-size: 8pt; }
    .khs .rapor-khs1 td, .khs .rapor-khs1 th { padding: .8mm .65mm; }
    .khs .rapor-khs1 .kolom-sempit { font-size: 7pt; }
    /* Label pada kolom angka KHS harus utuh pada cetakan dan header berulang. */
    .khs .rapor-tabel th.kolom-predikat,
    .khs .rapor-tabel th.kolom-sks {
        white-space: nowrap;
        padding-left: .2mm;
        padding-right: .2mm;
    }
    .khs .rapor-tabel th.kolom-predikat { font-size: 7pt; }
    .khs .rapor-khs1 th.kolom-predikat { font-size: 6.4pt; }
    .khs .rapor-khs1 th.kolom-sks { font-size: 7pt; }
    /* KHS1: nomor dan angka jumlah tidak boleh pecah menjadi dua baris. */
    .khs .rapor-khs1 th.kolom-nomor {
        font-size: 7pt;
        white-space: nowrap;
        padding-left: .15mm;
        padding-right: .15mm;
    }
    .khs .rapor-khs1 tr.total-khs th {
        font-size: 7.1pt;
        white-space: nowrap;
        padding: .35mm .15mm;
        vertical-align: middle;
    }
    /* Rekap lebih padat agar dapat mengikuti Jumlah pada halaman yang sama jika muat. */
    .khs .rekap-khs1 { font-size: 8pt; }
    .khs .rekap-khs1 td, .khs .rekap-khs1 th { padding: .5mm .4mm; }
    .khs .rapor-deskripsi { font-size: 8.3pt; }
    .khs .rapor-deskripsi td, .khs .rapor-deskripsi th { border: .45pt solid #616161; padding: .5mm .85mm; }
    .khs .sikap-nilai td { height: 34mm; vertical-align: middle; font-size: 9.5pt; padding: 2mm; }
    .khs .tabel-sikap { page-break-inside: avoid; }
    .khs .bingkai { border: .45pt solid #616161; padding: 1.6mm; margin: 0 0 2mm; }
    .khs .kotak-isian { width: 100%; }
    .khs .kotak-isian td { height: 22mm; border: .45pt solid #616161; padding: 2mm; vertical-align: top; }
    .khs .kotak-isian .tanggapan { height: 27mm; }
    .khs .rapor-tabel .isian-manual td { height: 8mm; vertical-align: middle; }
    .khs .ttd { margin-top: 6mm; }
    .khs .ttd .kepala { text-align: center; padding-top: 4mm; }
</style>
<?= $this->endSection() ?>

<?= $this->section('isi') ?>
<div class="khs">
    <?php if ($khs1): ?><h1><?= esc($judul) ?></h1><?php endif ?>
    <?php $lebarIdentitas = $khs1 ? [19, 45, 18, 18] : [16, 40, 24, 20]; ?>
    <table class="isian<?= $khs1 ? '' : ' identitas-khs2' ?>">
        <?php foreach ($identitas as [$kiri, $nilaiKiri, $kanan, $nilaiKanan]): ?>
            <tr>
                <td style="width: <?= $lebarIdentitas[0] ?>%"><?= esc($kiri) ?></td><td style="width: <?= $lebarIdentitas[1] ?>%"><?= esc($nilaiKiri) ?></td>
                <td style="width: <?= $lebarIdentitas[2] ?>%"><?= esc($kanan) ?></td><td style="width: <?= $lebarIdentitas[3] ?>%"><?= esc($nilaiKanan) ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php if (! $khs1): ?><h1><?= esc($judul) ?></h1><?php endif ?>

    <h2>A. Sikap</h2>
    <?php foreach (['Sikap Spiritual', 'Sikap Sosial'] as $i => $nama): ?>
        <h3><?= $i + 1 ?>. <?= esc($nama) ?></h3>
        <?php $items = $pendukung[mb_strtolower($nama)] ?? [[]]; ?>
        <?php if ($khs1): ?>
            <?php foreach ($items as $item): ?>
                <div class="bingkai"><?= $teks($item) === '' ? '&nbsp;' : nl2br(esc($teks($item)), false) ?></div>
            <?php endforeach ?>
        <?php else: ?>
            <table class="rapor-tabel tabel-sikap">
                <thead><tr><th style="width: 24%">Predikat</th><th>Deskripsi</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr class="sikap-nilai"><td><strong><?= esc($item['predikat'] ?? '') ?></strong></td><td><?= nl2br(esc($teks($item)), false) ?></td></tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    <?php endforeach ?>

    <?php if (! $khs1): ?><pagebreak /><?php endif ?>
    <h2>B. Pengetahuan dan Keterampilan</h2>
    <p><?= $khs1 ? 'Ketuntasan Belajar Minimal' : 'Kriteria Ketuntasan Minimal' ?>&nbsp; <?= esc($tampil($batas[$khs1 ? 'KBM' : 'KKM'] ?? null)) ?></p>
    <?= view('cetak/rapor/tabel_mapel', compact('mapel', 'kolom', 'tampil', 'potongan', 'khs') + [
        'lebarNo' => $khs1 ? 3.5 : 4,
        'lebarMapel' => $khs1 ? 14 : 30,
        'lebarField' => $khs1 ? [
            'sks' => 4.5,
            'nilai_pengetahuan' => 4.5, 'sk_sx_n1' => 5.5, 'predikat_pengetahuan' => 6, 'deskripsi_pengetahuan' => 23,
            'nilai_keterampilan' => 4.5, 'sk_sx_n2' => 5.5, 'predikat_keterampilan' => 6, 'deskripsi_keterampilan' => 23,
        ] : [
            'beban_jp' => 7, 'nilai_pengetahuan' => 7, 'predikat_pengetahuan' => 8,
            'nilai_keterampilan' => 7, 'predikat_keterampilan' => 8, 'rata_rata_ard' => 14, 'bebanx_n' => 15,
        ],
        'padat' => $khs1,
        'total' => [
            'sks' => $rapor['total_sks'] ?? null, 'beban_jp' => $rapor['total_beban_ard'] ?? null,
            'nilai_pengetahuan' => $rapor['total_nilai_pengetahuan'] ?? null, 'sk_sx_n1' => $rapor['total_sk_sx_n1'] ?? null,
            'nilai_keterampilan' => $rapor['total_nilai_keterampilan'] ?? null, 'sk_sx_n2' => $rapor['total_sk_sx_n2'] ?? null,
            'bebanx_n' => $rapor['total_bebanx_n'] ?? null,
        ],
    ], ['saveData' => false]) ?>

    <?php if ($khs1): ?>
        <?php
        $ipk = $pendukung['ipk'][0] ?? [];
        $nilaiIpk = $teks($ipk);
        if ($nilaiIpk === '' && isset($ipk['nilai'])) {
            $nilaiIpk = number_format((float) $ipk['nilai'], 2, ',', '');
        }
        ?>
        <table class="rapor-tabel rekap-khs1" style="margin-top: 1.5mm; page-break-inside: avoid">
            <tr>
                <th style="width: 18%">Peringkat</th><th style="width: 12%">IP</th><th style="width: 20%">SKS Berikutnya</th><th style="width: 12%">IPK</th>
                <td rowspan="2" class="teks">IP = (Σ SKS × N1 + Σ SKS × N2) ÷ (2 × Σ SKS).<br>N1 = Pengetahuan. N2 = Keterampilan.</td>
            </tr>
            <tr>
                <td><?= esc($tampil($rapor['peringkat'] ?? null)) ?></td>
                <td><?= $ip === null ? '' : esc(number_format((float) $ip, 2, ',', '')) ?></td>
                <td><?= esc($tampil($rapor['sks_berikutnya'] ?? null)) ?></td>
                <td><?= esc($nilaiIpk) ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p><strong>IP Semester&nbsp; <?= esc($tampil($ip)) ?></strong></p>
        <?php if ($rentang !== []): ?>
            <h3>Rentang Predikat</h3>
            <table class="rapor-tabel" style="page-break-inside: avoid">
                <thead><tr><th>KKM</th><?php foreach ($rentang as $item): ?><th><?= esc($item['predikat'] ?? '') ?></th><?php endforeach ?></tr></thead>
                <tbody><tr><td><?= esc($tampil($batas['KKM'] ?? null)) ?></td><?php foreach ($rentang as $item): ?><td><?= esc($item['rentang_tercetak'] ?? '') ?></td><?php endforeach ?></tr></tbody>
            </table>
        <?php endif ?>
        <pagebreak />
        <h2>C. Deskripsi Pengetahuan dan Keterampilan</h2>
        <?= view('cetak/rapor/tabel_mapel', compact('mapel', 'tampil', 'potongan', 'khs') + [
            'kolom' => ['Pengetahuan' => 'deskripsi_pengetahuan', 'Keterampilan' => 'deskripsi_keterampilan'],
            'lebarNo' => 4,
            'lebarMapel' => 24,
            'lebarField' => ['deskripsi_pengetahuan' => 36, 'deskripsi_keterampilan' => 36],
            'padat' => false,
        ], ['saveData' => false]) ?>
    <?php endif ?>

    <?php // KHS1: data pendukung mengalir setelah rekap jika masih ada ruang; KHS2 tetap di halaman tersendiri. ?>
    <?php if (! $khs1): ?><pagebreak /><?php endif ?>
    <?= view('cetak/rapor/pendukung', compact('pendukung', 'pengesahan', 'tampil', 'potongan', 'khs1'), ['saveData' => false]) ?>
</div>
<?= $this->endSection() ?>
