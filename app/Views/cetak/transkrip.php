<?php
$angkaCetak = static function ($nilai): string {
    if ($nilai === null) {
        return '';
    }

    return rtrim(rtrim(number_format((float) $nilai, 2, ',', ''), '0'), ',');
};
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>
    <style>
        body { color: #171916; font-family: dejavusans, sans-serif; font-size: 8.5pt; }
        .kop { width: 100%; text-align: center; border-bottom: 1.3pt double #171916; margin-bottom: 4mm; }
        .kop td { padding: 0 0 1.4mm; }
        .kop-logo { width: 25mm; }
        .kop-nama { font-family: dejavuserif, serif; font-size: 12.8pt; font-weight: bold; }
        .kop-alamat { font-family: dejavuserif, serif; font-style: italic; font-size: 9pt; }
        h1 { margin: 0 0 5mm; text-align: center; font-size: 15pt; }
        h2 { margin: 0 0 1.4mm; font-size: 9pt; }
        .identitas { width: 100%; margin-bottom: 4mm; }
        .identitas td { padding: 0; vertical-align: top; }
        .nilai { width: 100%; border-collapse: collapse; margin-bottom: 4mm; font-size: 7.5pt; empty-cells: show; }
        .nilai th, .nilai td { border: .36pt solid #77766e; padding: .53mm .65mm; text-align: center; }
        .nilai th { background: #ebe8d9; }
        .nilai .no { width: 4%; }
        .nilai .mapel { width: 30%; text-align: left; }
        .nilai .angka, .nilai .huruf { width: 8.3mm; font-size: 6.15pt; }
        .nilai .rata { width: 9%; background: #f6f4ea; font-weight: bold; }
        .nilai.padat { font-size: 7.1pt; margin-bottom: 2.6mm; }
        .nilai.padat th, .nilai.padat td { padding: .3mm .55mm; }
        .catatan { margin: 1mm 0; font-size: 7.4pt; color: #474940; }
        .tanda-tangan { width: 62mm; margin: 6mm 3mm 0 auto; page-break-inside: avoid; }
        .tanda-tangan p { margin: 0 0 1mm; }
        .nama-kepala { padding-top: 13mm; font-weight: bold; }
    </style>
</head>
<body>
    <table class="kop" aria-label="Kop madrasah">
        <tr>
            <td class="kop-logo"><img src="var:kopKemenag" style="width: 22mm" alt="Logo Kementerian Agama"></td>
            <td>
                <strong>KEMENTERIAN AGAMA REPUBLIK INDONESIA</strong><br>
                <span class="kop-nama">MTsN 4 JOMBANG</span><br>
                <span class="kop-alamat">JL. KH. BISHRI SYANSURI NO. 77 JOMBANG<br>
                    Kecamatan Jombang, Kabupaten Jombang - Jawa Timur</span>
            </td>
            <td class="kop-logo"></td>
        </tr>
    </table>
    <h1>TRANSKRIP NILAI</h1>
    <table class="identitas">
        <tr>
            <td style="width: 57%"><strong>Nama Siswa</strong> &nbsp; <?= esc($siswa['nama']) ?></td>
            <td><strong>No. Induk / NISN</strong> &nbsp; <?= esc(($siswa['nis'] ?? '—') . ' / ' . ($siswa['nisn'] ?? '—')) ?></td>
        </tr>
    </table>
    <?php foreach (['pengetahuan' => $judulPengetahuan, 'keterampilan' => 'B. Nilai Keterampilan'] as $bagian => $judul): ?>
        <h2><?= esc($judul) ?></h2>
        <table class="nilai<?= count($mapel) > 17 ? ' padat' : '' ?>" autosize="1.08">
            <thead>
                <tr>
                    <th class="no" rowspan="3">No.</th>
                    <th class="mapel" rowspan="3">Mata Pelajaran</th>
                    <th colspan="12">Semester</th>
                    <th class="rata" rowspan="3">Rata-rata</th>
                </tr>
                <tr>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <th colspan="2"><?= $i ?></th>
                    <?php endfor ?>
                </tr>
                <tr>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <th class="angka" nowrap="nowrap"><?= $bagian === 'pengetahuan' && in_array($i, $semesterMerdeka, true) ? 'Akhir' : 'Angka' ?></th>
                        <th class="huruf" nowrap="nowrap">Huruf</th>
                    <?php endfor ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapel as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td class="mapel"><?= esc($item['nama']) ?></td>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <?php $nilai = $item['semester'][$i][$bagian] ?? []; ?>
                            <td style="width: 8.3mm"><?= esc($angkaCetak($nilai['angka'] ?? null)) ?></td>
                            <td style="width: 8.3mm"><?= esc($nilai['huruf'] ?? '') ?></td>
                        <?php endfor ?>
                        <td class="rata"><?= esc($item['rata'][$bagian]) ?></td>
                    </tr>
                <?php endforeach ?>
                <?php if ($mapel === []): ?>
                    <tr><td colspan="15">Belum ada nilai yang tersedia.</td></tr>
                <?php endif ?>
            </tbody>
        </table>
    <?php endforeach ?>
    <p class="catatan">Rata-rata dihitung dari semester yang memiliki nilai.</p>
    <?php if ($semesterMerdeka !== []): ?>
        <p class="catatan">Tabel A memuat Nilai Akhir Kurikulum Merdeka untuk Semester <?= esc(implode(', ', $semesterMerdeka)) ?>.</p>
    <?php endif ?>
    <?php if ($semesterBelumDitemukan !== []): ?>
        <p class="catatan">Arsip belum ditemukan untuk Semester <?= esc(implode(', ', $semesterBelumDitemukan)) ?>.</p>
    <?php endif ?>
    <div class="tanda-tangan">
        <p><?= esc($pengesahan['tempatTanggal']) ?></p>
        <p>Kepala Madrasah,</p>
        <p class="nama-kepala"><?= esc($pengesahan['nama']) ?></p>
        <p>NIP <?= esc($pengesahan['nip']) ?></p>
    </div>
</body>
</html>
