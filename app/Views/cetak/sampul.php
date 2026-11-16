<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .sampul { text-align: center; }
    .judul-sampul { font-size: 22pt; margin: 8mm 0 0; }
    .jenjang { font-size: 16pt; font-weight: bold; margin: 4mm 0 0; }
    .pemilik { width: 85%; margin: 0 auto; text-align: left; font-size: 11pt; }
    .pemilik td { border: .6pt solid #444; padding: 3mm 4mm; }
</style>
<?= $this->endSection() ?>
<?= $this->section('isi') ?>
<div class="sampul">
    <div style="height: 10mm"></div>
    <img src="var:kopKemenag" style="width: 38mm" alt="Logo Kementerian Agama">
    <p><strong>KEMENTERIAN AGAMA REPUBLIK INDONESIA</strong></p>
    <h1 class="judul-sampul">LAPORAN HASIL BELAJAR</h1>
    <p class="jenjang">MADRASAH TSANAWIYAH<br>(MTs)</p>
    <div style="height: 73mm"></div>
    <table class="pemilik">
        <tr><td style="width: 34%"><strong>Nama</strong></td><td><?= esc($siswa['nama']) ?></td></tr>
        <tr><td><strong>NIS Madrasah</strong></td><td><?= esc($siswa['nis'] ?? '—') ?></td></tr>
        <tr><td><strong>NISN</strong></td><td><?= esc($siswa['nisn'] ?? '—') ?></td></tr>
    </table>
    <div style="height: 13mm"></div>
    <p class="jenjang">MTsN 4 JOMBANG</p>
    <p><strong>PROVINSI JAWA TIMUR</strong></p>
</div>
<?= $this->endSection() ?>
