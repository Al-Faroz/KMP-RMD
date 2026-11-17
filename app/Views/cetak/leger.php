<?php
$angka = static function (mixed $nilai): string {
    if ($nilai === null) {
        return '—';
    }
    $text = (string) $nilai;

    return str_contains($text, '.') ? str_replace('.', ',', rtrim(rtrim($text, '0'), '.')) : $text;
};
$judulLeger = 'LEGER KELAS' . ($jumlahBagian > 1 ? ' · Bagian ' . $bagian . ' dari ' . $jumlahBagian : '');
?>
<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .periode { width: 100%; margin: 2mm 0; }
    .periode td { padding: .7mm 0; }
    .leger { width: 100%; font-size: 7.5pt; text-align: center; }
    .leger td, .leger th { border: .4pt solid #666; padding: 1mm .5mm; }
    .leger th { background: #eee; }
    .leger .nama { text-align: left; width: 47mm; padding-left: 1.2mm; }
    .leger .kelas-mapel { width: 11mm; }
    .leger .status { font-size: 6.8pt; color: #444; }
    .keterangan { font-size: 7.6pt; margin: 2mm 0 3mm; }
    .ttd-leger { width: 78%; margin: 5mm auto 0; text-align: center; page-break-inside: avoid; }
    .ttd-leger td { width: 50%; }
    .ttd-leg-nama { padding-top: 10mm; font-weight: bold; }
</style>
<?= $this->endSection() ?>
<?= $this->section('isi') ?>
<?= view('cetak/kop_siswa', [], ['saveData' => false]) ?>
<table class="periode">
    <tr>
        <td style="width: 28%">Rombel <?= esc($rombel) ?></td>
        <td style="width: 22%">Angkatan <?= $angkatan ?></td>
        <td style="width: 20%">Semester <?= $semester ?></td>
        <td>Tahun Pelajaran <?= esc($tahunPelajaran === '' ? '—' : $tahunPelajaran) ?></td>
    </tr>
</table>
<h1><?= esc($judulLeger) ?></h1>
<?php if ($batas !== []): ?>
    <p class="keterangan">
        <?php foreach ($batas as $label => $nilai): ?>
            <?= esc($label . ' ' . $nilai) ?>&nbsp;&nbsp;
        <?php endforeach ?>
    </p>
<?php endif ?>
<p class="keterangan">Merdeka memakai Nilai Akhir. ARD memakai rata-rata arsip jika tersedia. Pasangan angka menunjukkan Pengetahuan/Keterampilan. Tanda — berarti nilai belum tersedia.</p>
<table class="leger" autosize="1">
    <thead>
        <tr>
            <th colspan="<?= 2 + count($mapel) + count($ringkasan) + (int) ($mapel === []) ?>">
                Rombel <?= esc($rombel) ?> · Angkatan <?= $angkatan ?> · Semester <?= $semester ?>
                <?= $jumlahBagian > 1 ? ' · Bagian ' . $bagian . ' dari ' . $jumlahBagian : '' ?>
            </th>
        </tr>
        <tr>
            <th style="width: 8mm">No.</th><th class="nama">Nama Siswa</th>
            <?php foreach ($mapel as $item): ?><th class="kelas-mapel"><?= esc($item['label']) ?></th><?php endforeach ?>
            <?php foreach ($ringkasan as $label => $field): ?><th style="width: 13mm"><?= esc($label) ?></th><?php endforeach ?>
            <?php if ($mapel === []): ?><th>Status Arsip</th><?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($anggota as $i => $siswa): ?>
            <?php $ditemukan = $siswa['status_arsip'] === 'Ditemukan'; ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td class="nama">
                    <?= esc($siswa['nama']) ?>
                    <?php if ($periodeCampuran && $siswa['tahun_pelajaran'] !== null): ?>
                        <br><span class="status"><?= esc($siswa['tahun_pelajaran']) ?></span>
                    <?php endif ?>
                    <?php if (! $ditemukan && $mapel !== []): ?>
                        <br><span class="status">Arsip belum ditemukan</span>
                    <?php elseif ($ditemukan && $mapel !== [] && empty($nilaiSiswa[$siswa['id']])): ?>
                        <br><span class="status">Nilai belum tersedia</span>
                    <?php endif ?>
                </td>
                <?php foreach ($mapel as $kunci => $item): ?>
                    <td><?= esc($nilaiSiswa[$siswa['id']][$kunci] ?? '—') ?></td>
                <?php endforeach ?>
                <?php foreach ($ringkasan as $field): ?>
                    <td><?= esc($angka($ditemukan ? $siswa[$field] : null)) ?></td>
                <?php endforeach ?>
                <?php if ($mapel === []): ?>
                    <td><?= $ditemukan ? 'Nilai belum tersedia' : 'Arsip belum ditemukan' ?></td>
                <?php endif ?>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?php if ($bagian === $jumlahBagian): ?>
    <table class="ttd-leger">
        <tr><td></td><td><?= esc(implode(', ', array_filter([$pengesahan['tempat'], $pengesahan['tanggal']]))) ?></td></tr>
        <tr><td>Kepala Madrasah,</td><td><?= esc($pengesahan['jabatan'] ?? 'Pembimbing Akademik') ?>,</td></tr>
        <tr>
            <td class="ttd-leg-nama"><?= esc($pengesahan['kepala'] ?? '........................................') ?></td>
            <td class="ttd-leg-nama"><?= esc($pengesahan['nama'] ?? '........................................') ?></td>
        </tr>
        <tr>
            <td>NIP <?= esc($pengesahan['nipKepala'] ?? '........................................') ?></td>
            <td>NIP <?= esc($pengesahan['nip'] ?? '........................................') ?></td>
        </tr>
    </table>
<?php endif ?>
<?= $this->endSection() ?>
