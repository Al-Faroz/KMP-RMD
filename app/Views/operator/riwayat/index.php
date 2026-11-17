<?php

use App\Models\RiwayatArsipModel;
use CodeIgniter\I18n\Time;

$kembali = $semester === RiwayatArsipModel::IDENTITAS
    ? url_to('detail-siswa', $siswa['id'])
    : url_to('detail-semester', $siswa['id'], $semester);
?>
<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<a href="<?= esc($kembali, 'attr') ?>">Kembali ke <?= $semester === RiwayatArsipModel::IDENTITAS ? 'Detail Siswa' : 'Semester ' . $semester ?></a>
<p class="mt-2 mb-3"><?= esc($siswa['nama']) ?> · Angkatan <?= esc($siswa['tahun_masuk']) ?></p>

<?php if ($sukses = session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success" role="status"><?= esc($sukses) ?></div>
<?php endif ?>

<div class="card">
    <?php if ($daftar === []): ?>
        <p class="card-body mb-0">Belum ada perubahan yang tercatat.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th scope="col">Waktu (WIB)</th>
                        <th scope="col">Operator</th>
                        <th scope="col">Tindakan</th>
                        <th scope="col">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar as $item): ?>
                        <tr>
                            <td><?= esc(Time::parse($item['waktu'], 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s')) ?></td>
                            <td class="text-break"><?= esc($item['nama_operator']) ?></td>
                            <td><?= esc(RiwayatArsipModel::AKSI[$item['aksi']]) ?></td>
                            <td><a href="<?= esc(url_to('detail-riwayat', $siswa['id'], $item['id']), 'attr') ?>">Lihat Perubahan</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="card-body pb-0"><?= $pager->links('default', 'rmd') ?></div>
        <?php endif ?>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
