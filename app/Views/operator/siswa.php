<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<section class="card" aria-labelledby="judul-daftar-siswa">
    <div class="card-body">
        <form method="get" action="<?= esc(url_to('data-siswa'), 'attr') ?>" class="row g-3 align-items-end">
            <div class="col-md">
                <label for="kata-siswa" class="form-label">Nama, NIS, atau NISN</label>
                <input id="kata-siswa" name="q" type="search" class="form-control"
                       value="<?= esc($kata, 'attr') ?>" maxlength="100">
            </div>
            <div class="col-md-3">
                <label for="angkatan-siswa" class="form-label">Angkatan</label>
                <select id="angkatan-siswa" name="angkatan" class="form-select">
                    <option value="">Semua angkatan</option>
                    <?php foreach ($daftarAngkatan as $item): ?>
                        <?php $tahun = (string) $item['tahun_masuk']; ?>
                        <option value="<?= esc($tahun, 'attr') ?>" <?= $angkatan === $tahun ? 'selected' : '' ?>><?= esc($tahun) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
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
