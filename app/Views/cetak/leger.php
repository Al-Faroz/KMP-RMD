<?php
$angka = static function (mixed $nilai): string {
    if ($nilai === null) {
        return '—';
    }
    $text = (string) $nilai;

    return str_contains($text, '.') ? str_replace('.', ',', rtrim(rtrim($text, '0'), '.')) : $text;
};
$judulLeger = 'LEGER KELAS' . ($jumlahBagian > 1 ? ' · Bagian ' . $bagian . ' dari ' . $jumlahBagian : '');
$labelNilai = [];
$jumlahKolomNilai = 0;
foreach ($mapel as $item) {
    $labelNilai += $item['kolom'];
    $jumlahKolomNilai += max(1, count($item['kolom']));
}
$jumlahKolom = 3 + $jumlahKolomNilai + (int) $adaTotalAkhir + (int) $periodeCampuran + (int) ($mapel === []);
$lebarNilai = (306 - 8 - 56 - 16 - ($adaTotalAkhir ? 16 : 0) - ($periodeCampuran ? 20 : 0)) / max(1, $jumlahKolomNilai);
$tinggiKepala = $labelNilai === [] ? 1 : 2;
?>
<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .periode { width: 100%; margin: 2mm 0; }
    .periode td { padding: .7mm 0; }
    .leger { width: 100%; font-size: 7.5pt; text-align: center; }
    .leger td, .leger th { border: .4pt solid #666; padding: 1mm .5mm; }
    .leger th { background: #eee; }
    .leger .nama { text-align: left; width: 56mm; padding-left: 1.2mm; }
    .leger .nilai-mapel { width: <?= round($lebarNilai, 3) ?>mm; }
    .leger .ip, .leger .total-akhir { width: 16mm; }
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
<p class="keterangan">
    <?= isset($labelNilai['nilai_pengetahuan']) ? 'P = Pengetahuan, K = Keterampilan. ' : '' ?>
    <?= isset($labelNilai['nilai_akhir']) ? 'Akhir = Nilai Akhir. ' : '' ?>
    <?= isset($labelNilai['rata_rata_ard']) ? 'Rata ARD = rata-rata dari arsip ARD. ' : '' ?>
    IP Semester mengikuti arsip. Tanda — berarti nilai belum tersedia.
</p>
<table class="leger" autosize="1">
    <thead>
        <tr>
            <th colspan="<?= $jumlahKolom ?>">
                Rombel <?= esc($rombel) ?> · Angkatan <?= $angkatan ?> · Semester <?= $semester ?>
                <?= $jumlahBagian > 1 ? ' · Bagian ' . $bagian . ' dari ' . $jumlahBagian : '' ?>
            </th>
        </tr>
        <tr>
            <th rowspan="<?= $tinggiKepala ?>" style="width: 8mm">No.</th>
            <th rowspan="<?= $tinggiKepala ?>" class="nama">Nama Siswa</th>
            <?php if ($periodeCampuran): ?><th rowspan="<?= $tinggiKepala ?>" style="width: 20mm">Tahun Pelajaran</th><?php endif ?>
            <?php foreach ($mapel as $item): ?>
                <?php if ($item['kolom'] === []): ?>
                    <th class="nilai-mapel" rowspan="<?= $tinggiKepala ?>"><?= esc($item['label']) ?></th>
                <?php else: ?>
                    <th colspan="<?= count($item['kolom']) ?>"><?= esc($item['label']) ?></th>
                <?php endif ?>
            <?php endforeach ?>
            <?php if ($adaTotalAkhir): ?><th class="total-akhir" rowspan="<?= $tinggiKepala ?>">Total Nilai Akhir</th><?php endif ?>
            <th class="ip" rowspan="<?= $tinggiKepala ?>">IP Semester</th>
            <?php if ($mapel === []): ?><th>Status Arsip</th><?php endif ?>
        </tr>
        <?php if ($tinggiKepala === 2): ?>
            <tr>
                <?php foreach ($mapel as $item): ?>
                    <?php foreach ($item['kolom'] as $label): ?><th class="nilai-mapel"><?= esc($label) ?></th><?php endforeach ?>
                <?php endforeach ?>
            </tr>
        <?php endif ?>
    </thead>
    <tbody>
        <?php foreach ($anggota as $i => $siswa): ?>
            <?php $ditemukan = $siswa['status_arsip'] === 'Ditemukan'; ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td class="nama">
                    <?= esc($siswa['nama']) ?>
                    <?php if (! $ditemukan && $mapel !== []): ?>
                        <br><span class="status">Arsip belum ditemukan</span>
                    <?php elseif ($ditemukan && $mapel !== [] && empty($nilaiSiswa[$siswa['id']])): ?>
                        <br><span class="status">Nilai belum tersedia</span>
                    <?php endif ?>
                </td>
                <?php if ($periodeCampuran): ?><td><?= esc($siswa['tahun_pelajaran'] ?? '—') ?></td><?php endif ?>
                <?php foreach ($mapel as $kunci => $item): ?>
                    <?php if ($item['kolom'] === []): ?><td class="nilai-mapel">—</td><?php endif ?>
                    <?php foreach ($item['kolom'] as $field => $label): ?>
                        <td class="nilai-mapel"><?= esc($angka($nilaiSiswa[$siswa['id']][$kunci][$field] ?? null)) ?></td>
                    <?php endforeach ?>
                <?php endforeach ?>
                <?php if ($adaTotalAkhir): ?><td class="total-akhir"><?= esc($angka($ditemukan ? $siswa['total_nilai_akhir'] : null)) ?></td><?php endif ?>
                <td class="ip"><?= $ditemukan && $siswa['ip_semester'] !== null ? number_format((float) $siswa['ip_semester'], 2, ',', '') : '—' ?></td>
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
