<?= $this->extend('cetak/layout_siswa') ?>
<?= $this->section('style') ?>
<style>
    .foto { border: .7pt solid #555; width: 30mm; height: 40mm; text-align: center; vertical-align: middle; }
    .identitas .isian td { padding-top: .75mm; padding-bottom: .75mm; }
</style>
<?= $this->endSection() ?>
<?= $this->section('isi') ?>
<div class="identitas">
<?= view('cetak/kop_siswa', [], ['saveData' => false]) ?>
<h1>IDENTITAS PESERTA DIDIK</h1>
<table class="isian">
    <?php foreach ([
        ['1.', 'Nama Peserta Didik', $siswa['nama']],
        ['2.', 'NIS', $siswa['nis']],
        ['3.', 'NISN', $siswa['nisn']],
        ['4.', 'Tempat dan Tanggal Lahir', $lahir],
        ['5.', 'Jenis Kelamin', $siswa['jenis_kelamin']],
        ['6.', 'Agama', $siswa['agama']],
        ['7.', 'Status dalam Keluarga', $siswa['status_dalam_keluarga']],
        ['8.', 'Anak ke', $siswa['anak_ke']],
        ['9.', 'Alamat Peserta Didik', $siswa['alamat_siswa']],
        ['10.', 'Nomor Telepon Rumah/HP', $siswa['telepon_siswa']],
        ['11.', 'Sekolah Asal', $siswa['sekolah_asal']],
        ['12.', 'Diterima di madrasah ini', null],
        ['', 'a. Di kelas', $siswa['kelas_diterima']],
        ['', 'b. Pada tanggal', $diterima],
        ['13.', 'Nama Orang Tua', null],
        ['', 'a. Ayah', $siswa['nama_ayah']],
        ['', 'b. Ibu', $siswa['nama_ibu']],
        ['14.', 'Alamat Orang Tua', $siswa['alamat_orang_tua']],
        ['', 'Nomor Telepon Orang Tua', $siswa['telepon_orang_tua']],
        ['15.', 'Pekerjaan Orang Tua', null],
        ['', 'a. Ayah', $siswa['pekerjaan_ayah']],
        ['', 'b. Ibu', $siswa['pekerjaan_ibu']],
        ['16.', 'Nama Wali Siswa', $siswa['nama_wali']],
        ['', 'Pekerjaan Wali', $siswa['pekerjaan_wali']],
        ['', 'Alamat Wali Siswa', $siswa['alamat_wali']],
        ['', 'Nomor Telepon Wali', $siswa['telepon_wali']],
    ] as [$no, $label, $isi]): ?>
        <tr>
            <td style="width: 6%"><?= $no ?></td>
            <td class="label<?= $no === '' ? ' sub' : '' ?>"><?= esc($label) ?></td>
            <td><?= in_array($no, ['12.', '13.', '15.'], true) ? '' : ':' ?></td>
            <td><?= nl2br(esc($isi ?? ''), false) ?></td>
        </tr>
    <?php endforeach ?>
</table>
<table class="ttd">
    <tr>
        <td class="foto" rowspan="4">Pasfoto<br>3 × 4 cm</td>
        <td style="width: 46%"></td>
        <td><?= esc($pengesahan['tempat']) ?><?= $pengesahan['tanggal'] === null ? '' : ', ' . esc($pengesahan['tanggal']) ?></td>
    </tr>
    <tr><td></td><td>Kepala Madrasah,</td></tr>
    <tr><td></td><td class="ttd-nama"><?= esc($pengesahan['nama'] ?? '........................................') ?></td></tr>
    <tr><td></td><td>NIP <?= esc($pengesahan['nip'] ?? '........................................') ?></td></tr>
</table>
</div>
<?= $this->endSection() ?>
