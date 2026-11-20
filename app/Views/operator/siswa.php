<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<section class="card" aria-labelledby="judul-daftar-siswa">
    <div class="card-body">
        <form method="get" action="<?= esc(url_to('data-siswa'), 'attr') ?>" class="row g-3 align-items-end">
            <?= view('siswa/pencarian', ['urlSaran' => url_to('saran-siswa'), 'kata' => $kata,
                'angkatan' => $angkatan, 'daftarAngkatan' => $daftarAngkatan], ['saveData' => false]) ?>
            <div class="col-md-auto d-flex gap-2">
                <button class="btn btn-primary" type="submit">Cari</button>
                <?php if ($kata !== '' || $angkatan !== ''): ?>
                    <a class="btn btn-outline-secondary" href="<?= esc(url_to('data-siswa'), 'attr') ?>">Reset</a>
                <?php endif ?>
            </div>
        </form>
    </div>
    <div class="card-body border-top py-3">
        <h2 id="judul-daftar-siswa" class="h5 mb-1">Daftar Siswa</h2>
        <p class="mb-0"><?= number_format($pager->getTotal(), 0, ',', '.') ?> siswa</p>
    </div>

    <?php if ($siswa === []): ?>
        <p class="px-4 pb-3 mb-0">Tidak ada siswa yang cocok.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">NIS</th>
                        <th scope="col">NISN</th>
                        <th scope="col">Angkatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswa as $item): ?>
                        <tr>
                            <th scope="row" class="fw-semibold text-none">
                                <a href="<?= esc(url_to('detail-siswa', $item['id']), 'attr') ?>"><?= esc($item['nama']) ?></a>
                            </th>
                            <td><?= esc($item['nis'] ?? '—') ?></td>
                            <td><?= esc($item['nisn'] ?? '—') ?></td>
                            <td><?= esc($item['tahun_masuk']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="card-body border-top py-3">
                <?= $pager->links('default', 'rmd') ?>
            </div>
        <?php endif ?>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
