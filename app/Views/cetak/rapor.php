<?php
$tampil = static function (mixed $nilai): string {
    $teks = (string) $nilai;

    return preg_match('/^-?\d+\.\d+$/D', $teks) === 1 ? str_replace('.', ',', rtrim(rtrim($teks, '0'), '.')) : $teks;
};
$potongan = static function (?string $teks): array {
    if ($teks === null || mb_strlen($teks) <= 380) {
        return [$teks ?? ''];
    }

    $hasil = [];
    while (mb_strlen($teks) > 380) {
        $awal = mb_substr($teks, 0, 380);
        $akhir = mb_strrpos($awal, ' ');
        $batas = $akhir !== false && $akhir >= 190 ? $akhir : 380;
        $hasil[] = mb_substr($teks, 0, $batas);
        $teks = ltrim(mb_substr($teks, $batas));
    }
    if ($teks !== '') {
        $hasil[] = $teks;
    }

    return $hasil;
};
?>
<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    h2 { font-size: 10pt; margin: 4mm 0 1.7mm; page-break-after: avoid; }
    .rapor-tabel { width: 100%; text-align: center; font-size: 8pt; }
    .rapor-tabel th, .rapor-tabel td { border: .4pt solid #555; padding: 1.05mm .8mm; vertical-align: top; }
    .rapor-tabel th { background: #eeeeee; vertical-align: middle; }
    .rapor-tabel .teks { text-align: left; }
    .rapor-tabel .kolom-sempit { font-size: 6.7pt; }
    .rapor-tabel .kelompok, .rapor-tabel .subkelompok { text-align: left; font-weight: bold; }
    .kelompok { background: #f2f2f2; }
    .subkelompok { background: #fafafa; }
    .ringkasan { width: 54%; margin-top: 2mm; }
    .ringkasan td { padding: .6mm 1mm; }
    .bingkai { border: .4pt solid #555; padding: 2mm; min-height: 10mm; margin: 0; }
    .ttd { margin-top: 7mm; }
    .ttd .kepala { text-align: center; padding-top: 3mm; }
</style>
<?= $this->endSection() ?>
<?= $this->section('isi') ?>
<?php if ($merdeka): ?>
    <?= view('cetak/kop_siswa', [], ['saveData' => false]) ?>
<?php endif ?>
<table class="isian" style="margin-top: 2mm">
    <tr>
        <td style="width: 17%">Nama</td><td style="width: 36%">: <?= esc($siswa['nama']) ?></td>
        <td style="width: 24%">Madrasah</td><td>: <?= esc($pengaturan['nama_madrasah'] ?? 'MTsN 4 Jombang') ?></td>
    </tr>
    <tr>
        <td>NIS</td><td>: <?= esc($siswa['nis'] ?? '—') ?></td>
        <td>Kelas / Semester</td><td>: <?= esc($rapor['rombel'] ?? $rapor['kelas'] ?? '—') ?> / <?= esc($rapor['semester']) ?></td>
    </tr>
    <tr>
        <td>NISN</td><td>: <?= esc($siswa['nisn'] ?? '—') ?></td>
        <td>Tahun Pelajaran</td><td>: <?= esc($rapor['tahun_pelajaran'] ?? '—') ?></td>
    </tr>
    <?php if ($merdeka && ! empty($pengaturan['alamat_madrasah'])): ?>
        <tr><td>Alamat</td><td colspan="3">: <?= esc($pengaturan['alamat_madrasah']) ?></td></tr>
    <?php endif ?>
</table>
<h1><?= esc($judul) ?></h1>
<?php if (! $merdeka): ?>
    <h2>A. Sikap</h2>
    <table class="rapor-tabel">
        <thead><tr><th style="width: 22%">Sikap</th><th style="width: 14%">Predikat</th><th>Deskripsi</th></tr></thead>
        <tbody>
            <?php foreach (['Sikap Spiritual', 'Sikap Sosial'] as $sikap): ?>
                <?php foreach ($pendukung[mb_strtolower($sikap)] ?? [[]] as $item): ?>
                    <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $segmen => $isi): ?>
                        <tr>
                            <td class="teks"><?= esc($sikap) ?></td><td><?= esc($segmen === 0 ? ($item['predikat'] ?? '') : '') ?></td>
                            <td class="teks"><?= nl2br(esc($isi), false) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
            <?php endforeach ?>
        </tbody>
    </table>
    <h2>B. Pengetahuan dan Keterampilan</h2>
<?php endif ?>
<?php if ($batas !== []): ?>
    <p>
        <?php foreach ($batas as $label => $nilai): ?>
            <?= esc($label) ?> <?= esc($tampil($nilai)) ?>&nbsp;&nbsp;
        <?php endforeach ?>
    </p>
<?php endif ?>
<?= view('cetak/rapor/tabel_mapel', [
    'kolom' => $kolom, 'mapel' => $mapel, 'tampil' => $tampil, 'potongan' => $potongan,
    'lebarMapel' => match (true) {
        $merdeka => 27,
        isset($kolom['SKS']) => 16,
        default => 26,
    },
    'lebarField' => isset($kolom['SKS']) ? [
        'sks' => 5,
        'nilai_pengetahuan' => 5, 'sk_sx_n1' => 6, 'predikat_pengetahuan' => 8, 'deskripsi_pengetahuan' => 18,
        'nilai_keterampilan' => 5, 'sk_sx_n2' => 6, 'predikat_keterampilan' => 8, 'deskripsi_keterampilan' => 18,
    ] : [],
], ['saveData' => false]) ?>
<?php if ($ringkasan !== []): ?>
    <table class="ringkasan">
        <?php foreach ($ringkasan as $label => $nilai): ?>
            <tr><td><?= esc($label) ?></td><td style="text-align: right"><?= esc($tampil($nilai)) ?></td></tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
<?php if ($rentang !== []): ?>
    <h2>Rentang Predikat</h2>
    <table class="rapor-tabel">
        <thead><tr><th>Predikat</th><?php foreach ($rentang as $item): ?><th><?= esc($item['predikat'] ?? '') ?></th><?php endforeach ?></tr></thead>
        <tbody><tr><td>Rentang</td><?php foreach ($rentang as $item): ?><td><?= esc($item['rentang_tercetak'] ?? '') ?></td><?php endforeach ?></tr></tbody>
    </table>
<?php endif ?>
<?php if ($deskripsiTerpisah): ?>
    <h2>C. Deskripsi Pengetahuan dan Keterampilan</h2>
    <?= view('cetak/rapor/tabel_mapel', [
        'kolom' => ['Pengetahuan' => 'deskripsi_pengetahuan', 'Keterampilan' => 'deskripsi_keterampilan'],
        'mapel' => $mapel, 'tampil' => $tampil, 'potongan' => $potongan, 'lebarMapel' => 27, 'lebarField' => [],
    ], ['saveData' => false]) ?>
<?php endif ?>
<?= view('cetak/rapor/pendukung', [
    'pendukung' => $pendukung, 'pengaturan' => $pengaturan, 'rapor' => $rapor,
    'tempatTanggal' => $tempatTanggal, 'tampil' => $tampil, 'potongan' => $potongan,
], ['saveData' => false]) ?>
<?= $this->endSection() ?>
