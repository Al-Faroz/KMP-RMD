<?= $this->extend('layouts/publik') ?>

<?= $this->section('content') ?>
<div class="container rmd-public-wrap py-4 py-md-5">
    <a class="rmd-public-back" href="<?= esc(url_to('beranda-publik'), 'attr') ?>">&larr; Cari Siswa</a>
    <h1 class="h2 mt-3 mb-1 text-break"><?= esc($siswa['nama']) ?></h1>
    <p class="mb-4">Angkatan <?= esc($siswa['tahun_masuk']) ?></p>

    <section class="card rmd-public-panel mb-4" aria-labelledby="judul-rapor">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
                <h2 id="judul-rapor" class="h4 mb-0">Rapor Semester</h2>
                <?php if ($semesterDenganRapor !== []): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                        data-url-cetak="<?= esc(url_to('semua-semester-publik', $siswa['id']), 'attr') ?>">Cetak Semua Semester</button>
                <?php endif ?>
            </div>
            <?php if ($semesterTersedia === []): ?>
                <p class="mb-0">Belum ada rapor semester yang tersedia.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($semesterTersedia as $semester): ?>
                        <li class="list-group-item px-0 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                            <strong>Semester <?= esc($semester) ?></strong>
                            <div class="rmd-public-actions">
                                <?php if (in_array($semester, $semesterDenganRapor, true)): ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                                        data-url-cetak="<?= esc(url_to('rapor-publik', $siswa['id'], $semester), 'attr') ?>"
                                        aria-label="Cetak Rapor Semester <?= esc($semester, 'attr') ?>">Cetak Rapor</button>
                                <?php endif ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                                    data-url-cetak="<?= esc(url_to('nilai-publik', $siswa['id'], $semester), 'attr') ?>"
                                    aria-label="Cetak Lembar Nilai Semester <?= esc($semester, 'attr') ?>">Cetak Lembar Nilai</button>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </div>
    </section>

    <section class="card rmd-public-panel" aria-labelledby="judul-dokumen">
        <div class="card-body p-3 p-md-4">
            <h2 id="judul-dokumen" class="h4 mb-3">Dokumen Lainnya</h2>
            <div class="rmd-public-actions">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                    data-url-cetak="<?= esc(url_to('transkrip-publik', $siswa['id']), 'attr') ?>">Cetak Transkrip</button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                    data-url-cetak="<?= esc(url_to('sampul-publik', $siswa['id']), 'attr') ?>">Cetak Sampul</button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-kode-cetak"
                    data-url-cetak="<?= esc(url_to('identitas-publik', $siswa['id']), 'attr') ?>">Cetak Identitas</button>
            </div>
        </div>
    </section>
</div>
<?= $this->include('publik/modal_kode_cetak') ?>
<?= $this->endSection() ?>
