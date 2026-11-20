<?= $this->extend('layouts/publik') ?>

<?= $this->section('content') ?>
<section class="rmd-public-hero" aria-labelledby="judul-arsip">
    <div class="container rmd-public-wrap">
        <h1 id="judul-arsip">Arsip Rapor Siswa</h1>
        <dl class="rmd-public-stats mb-0" aria-label="Statistik arsip">
            <div>
                <dt>Siswa</dt>
                <dd><?= number_format($jumlahSiswa, 0, ',', '.') ?></dd>
            </div>
            <div>
                <dt>Rapor Tersedia</dt>
                <dd><?= number_format($jumlahRapor, 0, ',', '.') ?></dd>
            </div>
            <div>
                <dt>Angkatan</dt>
                <dd><?= number_format(count($daftarAngkatan), 0, ',', '.') ?></dd>
            </div>
        </dl>
    </div>
</section>
<div class="container rmd-public-wrap py-4">
    <section class="card rmd-public-panel" aria-labelledby="judul-pencarian">
        <div class="card-body p-3 p-md-4">
            <h2 id="judul-pencarian" class="h4 mb-1">Cari Siswa</h2>
            <p class="mb-4">Pilih nama siswa untuk membuka pilihan cetak PDF.</p>
            <form method="get" action="<?= esc(url_to('beranda-publik'), 'attr') ?>" class="row g-3 align-items-end">
                <?= view('siswa/pencarian', ['urlSaran' => url_to('saran-publik'), 'kata' => $kata,
                    'angkatan' => $angkatan, 'daftarAngkatan' => $daftarAngkatan], ['saveData' => false]) ?>
                <div class="col-md-auto">
                    <button class="btn btn-primary w-100" type="submit">Cari</button>
                </div>
            </form>

            <?php if ($kata !== ''): ?>
                <div class="mt-4 border-top pt-3" aria-live="polite">
                    <?php if (mb_strlen($kata) < 2): ?>
                        <p class="mb-0">Masukkan minimal 2 karakter.</p>
                    <?php elseif ($hasil === []): ?>
                        <p class="mb-0">Tidak ada siswa yang cocok.</p>
                    <?php else: ?>
                        <ul class="list-group" aria-label="Hasil pencarian siswa">
                            <?php foreach ($hasil as $siswa): ?>
                                <li class="list-group-item">
                                    <a class="d-flex flex-wrap justify-content-between gap-2"
                                       href="<?= esc(url_to('dokumen-publik', $siswa['id']), 'attr') ?>">
                                        <strong><?= esc($siswa['nama']) ?></strong>
                                        <span>Angkatan <?= esc($siswa['tahun_masuk']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
