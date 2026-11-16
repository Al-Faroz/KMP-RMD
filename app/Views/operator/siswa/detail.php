<?php use App\Models\SiswaModel; ?>

<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between gap-3 align-items-start mb-4">
    <div>
        <a href="<?= esc(url_to('data-siswa'), 'attr') ?>">Kembali ke Data Siswa</a>
        <h2 class="h4 mt-2 mb-1"><?= esc($siswa['nama']) ?></h2>
        <p class="mb-0">Angkatan <?= esc($siswa['tahun_masuk']) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if ($adaRaporTersedia): ?>
            <a href="<?= esc(url_to('cetak-semua-semester', $siswa['id']), 'attr') ?>" target="_blank" rel="noopener"
               class="btn btn-outline-primary">Cetak Semua Semester</a>
        <?php endif ?>
        <a href="<?= esc(url_to('cetak-transkrip', $siswa['id']), 'attr') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">Cetak Transkrip</a>
        <a href="<?= esc(url_to('cetak-sampul', $siswa['id']), 'attr') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">Cetak Sampul</a>
        <a href="<?= esc(url_to('cetak-identitas', $siswa['id']), 'attr') ?>" target="_blank" rel="noopener" class="btn btn-outline-primary">Cetak Identitas</a>
        <a href="<?= esc(url_to('edit-siswa', $siswa['id']), 'attr') ?>" class="btn btn-primary">Edit Identitas</a>
    </div>
</div>

<?php if ($pesan = session()->getFlashdata('pesan_siswa')): ?>
    <div class="alert alert-success" role="status"><?= esc($pesan) ?></div>
<?php endif ?>

<section class="card mb-4" aria-labelledby="judul-riwayat">
    <div class="card-body">
        <h2 id="judul-riwayat" class="h5 mb-0">Riwayat Semester</h2>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Semester</th>
                    <th scope="col">Tahun pelajaran</th>
                    <th scope="col">Kelas</th>
                    <th scope="col">Rombel</th>
                    <th scope="col">Status arsip</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (range(1, 6) as $semester): ?>
                    <?php
                    $item = $riwayat[$semester] ?? [];
                    $status = $item['status_arsip'] ?? 'Belum ditemukan';
                    ?>
                    <tr>
                        <th scope="row" class="fw-semibold text-nowrap text-none">
                            <a href="<?= esc(url_to('detail-semester', $siswa['id'], $semester), 'attr') ?>">Semester <?= $semester ?></a>
                        </th>
                        <td><?= esc($item['tahun_pelajaran'] ?? '—') ?></td>
                        <td><?= esc($item['kelas'] ?? '—') ?></td>
                        <td><?= esc($item['rombel'] ?? '—') ?></td>
                        <td><?= view('operator/siswa/status_arsip', ['statusArsip' => $status], ['saveData' => false]) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>

<div class="card card-body">
    <?php foreach (SiswaModel::IDENTITAS as $judul => $fields): ?>
        <h2 class="h5 mb-3"><?= esc($judul) ?></h2>
        <dl class="row g-3 mb-4">
            <?php foreach ($fields as $field => [$label]): ?>
                <div class="col-sm-6 col-lg-4">
                    <dt><?= esc($label) ?></dt>
                    <dd class="mb-0 text-break"><?= esc($siswa[$field] ?? '—') ?></dd>
                </div>
            <?php endforeach ?>
        </dl>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
