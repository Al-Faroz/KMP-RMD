<?php
$jumlahKolom = 2 + count($kolom) + count($kolomPredikat);
$rowspan = $kolomPredikat === [] ? 1 : 2;
$angka = static function ($nilai): string {
    if ($nilai === null) {
        return '';
    }

    $text = (string) $nilai;

    return str_replace('.', ',', str_contains($text, '.') ? rtrim(rtrim($text, '0'), '.') : $text);
};
?>
<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .nilai { width: 100%; font-size: 8pt; text-align: center; }
    .nilai th, .nilai td { border: .45pt solid #777; padding: 1mm; }
    .nilai th { background-color: #eee; }
    .nilai .mapel { text-align: left; }
    .nilai .subkolom { font-size: 7.5pt; }
    .kelompok { text-align: left; font-weight: bold; background-color: #f4f4f4; }
    .ringkasan { width: 64%; margin-top: 2mm; }
    .ringkasan td { padding: .6mm 1mm; }
    .batas { margin: 1mm 0 2mm; font-size: 8pt; }
</style>
<?= $this->endSection() ?>
<?= $this->section('isi') ?>
<?= view('cetak/kop_siswa', [], ['saveData' => false]) ?>
<table class="isian" style="margin-top: 2mm">
    <tr>
        <td style="width: 12%">Nama</td><td style="width: 38%">: <?= esc($siswa['nama']) ?></td>
        <td style="width: 24%">Kelas/Semester</td>
        <td>: <?= esc($rapor['rombel'] ?? $rapor['kelas'] ?? '—') ?> / <?= esc($rapor['semester']) ?></td>
    </tr>
    <tr>
        <td>NISN</td><td>: <?= esc($siswa['nisn'] ?? '—') ?></td>
        <td>Tahun Pelajaran</td><td>: <?= esc($rapor['tahun_pelajaran'] ?? '—') ?></td>
    </tr>
</table>
<h1>KARTU HASIL STUDI</h1>
<?php if ($batasNilai !== []): ?>
    <p class="batas">
        <?php foreach ($batasNilai as $label => $item): ?>
            <span><?= esc($label) ?> <?= esc($angka($item)) ?></span>&nbsp;&nbsp;
        <?php endforeach ?>
    </p>
<?php endif ?>
<table class="nilai">
    <thead>
        <tr>
            <th style="width: 7%" rowspan="<?= $rowspan ?>">No.</th>
            <th class="mapel" rowspan="<?= $rowspan ?>">Mata Pelajaran</th>
            <?php foreach ($kolom as $nama => $label): ?>
                <?php if (isset($kolomPredikat[$nama])): ?>
                    <th colspan="2"><?= esc($label) ?></th>
                <?php else: ?>
                    <th rowspan="<?= $rowspan ?>"><?= esc($label) ?></th>
                <?php endif ?>
            <?php endforeach ?>
        </tr>
        <?php if ($kolomPredikat !== []): ?>
            <tr>
                <?php foreach ($kolomPredikat as $predikat): ?>
                    <th class="subkolom">Angka</th><th class="subkolom">Predikat</th>
                <?php endforeach ?>
            </tr>
        <?php endif ?>
    </thead>
    <tbody>
        <?php $kelompok = null; ?>
        <?php foreach ($nilai as $i => $item): ?>
            <?php if ($item['kelompok_mapel'] !== null && $item['kelompok_mapel'] !== $kelompok): ?>
                <tr><td class="kelompok" colspan="<?= $jumlahKolom ?>"><?= esc($item['kelompok_mapel']) ?></td></tr>
            <?php endif ?>
            <?php $kelompok = $item['kelompok_mapel']; ?>
            <tr>
                <td><?= $i + 1 ?></td><td class="mapel"><?= esc($item['nama_mapel_asli']) ?></td>
                <?php foreach (array_keys($kolom) as $nama): ?>
                    <td><?= esc($angka($item[$nama])) ?></td>
                    <?php if (isset($kolomPredikat[$nama])): ?>
                        <td><?= esc($item[$kolomPredikat[$nama]] ?? '') ?></td>
                    <?php endif ?>
                <?php endforeach ?>
            </tr>
        <?php endforeach ?>
        <?php if ($nilai === []): ?>
            <tr><td colspan="<?= $jumlahKolom ?>">Belum ada nilai yang tersedia.</td></tr>
        <?php endif ?>
    </tbody>
</table>
<?php if ($ringkasan !== []): ?>
    <table class="ringkasan">
        <?php foreach ($ringkasan as $label => $item): ?>
            <tr><td><?= esc($label) ?></td><td style="text-align: right"><?= esc($angka($item)) ?></td></tr>
        <?php endforeach ?>
    </table>
<?php endif ?>
<table class="ttd">
    <tr>
        <td style="width: 55%"></td>
        <td><?= esc($pengesahan['tempat']) ?><?= $pengesahan['tanggal'] === null ? '' : ', ' . esc($pengesahan['tanggal']) ?></td>
    </tr>
    <tr><td>Kepala Madrasah,</td><td><?= esc($rapor['jabatan_cetak'] ?? 'Pembimbing Akademik') ?>,</td></tr>
    <tr>
        <td class="ttd-nama"><?= esc($pengesahan['nama'] ?? '........................................') ?></td>
        <td class="ttd-nama"><?= esc($rapor['nama_penandatangan'] ?? '........................................') ?></td>
    </tr>
    <tr>
        <td>NIP <?= esc($pengesahan['nip'] ?? '........................................') ?></td>
        <td>NIP <?= esc($rapor['nip_penandatangan'] ?? '........................................') ?></td>
    </tr>
</table>
<?= $this->endSection() ?>
