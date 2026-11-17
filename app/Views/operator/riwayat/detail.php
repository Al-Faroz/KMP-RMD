<?php

use App\Models\RiwayatArsipModel;
use CodeIgniter\I18n\Time;

$identitas = $semester === RiwayatArsipModel::IDENTITAS;
$kembali = $identitas ? url_to('riwayat-identitas', $siswa['id']) : url_to('riwayat-rapor', $siswa['id'], $semester);
$lingkup = $identitas ? 'Identitas' : 'Semester ' . $semester;
?>
<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<a href="<?= esc($kembali, 'attr') ?>">Kembali ke Riwayat</a>
<h2 class="h5 mt-2 mb-1"><?= esc($siswa['nama']) ?> · <?= esc($lingkup) ?></h2>
<p class="mb-3">
    <?= esc(RiwayatArsipModel::AKSI[$riwayat['aksi']]) ?> ·
    <?= esc($riwayat['nama_operator']) ?> ·
    <?= esc(Time::parse($riwayat['waktu'], 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s')) ?> WIB
</p>

<?php if ($riwayat['dipulihkan_dari'] !== null): ?>
    <p><a href="<?= esc(url_to('detail-riwayat', $siswa['id'], $riwayat['dipulihkan_dari']), 'attr') ?>">Lihat perubahan yang dipulihkan</a></p>
<?php endif ?>
<?php if ($galat = session()->getFlashdata('galat')): ?>
    <div class="alert alert-danger" role="alert"><?= esc($galat) ?></div>
<?php endif ?>

<div class="card mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th scope="col">Bagian</th>
                    <th scope="col">Kolom</th>
                    <th scope="col">Sebelum</th>
                    <th scope="col">Sesudah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($perubahan as $item): ?>
                    <tr>
                        <td><?= esc($item['bagian']) ?></td>
                        <th scope="row"><?= esc($item['kolom']) ?></th>
                        <td class="text-break"><?= nl2br(esc($item['sebelum'] ?? '—')) ?></td>
                        <td class="text-break"><?= nl2br(esc($item['sesudah'] ?? '—')) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($bolehPulihkan): ?>
    <details class="card card-body">
        <summary class="fw-semibold">Pulihkan Versi Sebelumnya</summary>
        <form class="mt-3" method="post" action="<?= esc(url_to('detail-riwayat', $siswa['id'], $riwayat['id']), 'attr') ?>">
            <?= csrf_field() ?>
            <p>Data <?= esc($lingkup) ?> akan dikembalikan ke nilai pada kolom <strong>Sebelum</strong>. Pemulihan ini akan tercatat di riwayat.</p>
            <button class="btn btn-danger" type="submit">Konfirmasi Pemulihan</button>
        </form>
    </details>
<?php endif ?>
<?= $this->endSection() ?>
