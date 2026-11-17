<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<form class="card card-body mb-4" method="get" action="<?= esc(url_to('cetak-rombel'), 'attr') ?>" id="filter-rombel">
    <div class="row g-3 align-items-end">
        <div class="col-sm-4 col-lg">
            <label for="rombel-angkatan" class="form-label">Angkatan</label>
            <select id="rombel-angkatan" name="angkatan" class="form-select" required>
                <option value="">Pilih angkatan</option>
                <?php foreach ($daftarAngkatan as $item): ?>
                    <option value="<?= esc($item['tahun_masuk'], 'attr') ?>"<?= (int) $item['tahun_masuk'] === $angkatan ? ' selected' : '' ?>>
                        <?= esc($item['tahun_masuk']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-sm-4 col-lg">
            <label for="rombel-semester" class="form-label">Semester</label>
            <select id="rombel-semester" name="semester" class="form-select" required>
                <option value="">Pilih semester</option>
                <?php foreach (range(1, 6) as $nomor): ?>
                    <option value="<?= $nomor ?>"<?= $nomor === $semester ? ' selected' : '' ?>>Semester <?= $nomor ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-sm-4 col-lg">
            <label for="rombel-kelas" class="form-label">Rombel</label>
            <select id="rombel-kelas" name="rombel" class="form-select"<?= $daftarRombel === [] ? ' disabled' : '' ?>>
                <option value="">Pilih rombel</option>
                <?php foreach ($daftarRombel as $kode): ?>
                    <option value="<?= esc($kode, 'attr') ?>"<?= $kode === $rombel ? ' selected' : '' ?>><?= esc($kode) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-lg-auto"><button type="submit" class="btn btn-primary">Tampilkan</button></div>
    </div>
</form>

<?php if ($rombelTidakValid): ?>
    <p class="alert alert-warning">Rombel tidak tersedia untuk angkatan dan semester yang dipilih.</p>
<?php elseif ($angkatan !== 0 && $semester !== 0 && $daftarRombel === []): ?>
    <p class="alert alert-info">Belum ada rombel tercatat untuk angkatan dan semester yang dipilih.</p>
<?php endif ?>

<?php if ($rombel !== ''): ?>
    <?php
    $belum = count(array_filter($anggota, static fn (array $item): bool => $item['status_arsip'] !== 'Ditemukan'));
    $formatBelum = count($anggota) - $belum - count($tersedia);
    ?>
    <section class="card" aria-labelledby="judul-daftar-rombel">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2 id="judul-daftar-rombel" class="h5 mb-2">Rombel <?= esc($rombel) ?> · Semester <?= $semester ?></h2>
                <p class="mb-0"><?= count($anggota) ?> siswa · <?= count($tersedia) ?> siap cetak · <?= $belum ?> arsip belum ditemukan<?= $formatBelum > 0 ? ' · ' . $formatBelum . ' format belum tersedia' : '' ?></p>
            </div>
            <?php if ($anggota !== []): ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php $query = http_build_query(compact('angkatan', 'semester', 'rombel')); ?>
                    <?php if ($tersedia !== []): ?>
                        <a href="<?= esc(url_to('pdf-rombel') . '?' . $query, 'attr') ?>" class="btn btn-primary" target="_blank" rel="noopener">Buat PDF</a>
                    <?php endif ?>
                    <a href="<?= esc(url_to('pdf-leger') . '?' . $query, 'attr') ?>" class="btn btn-outline-primary" target="_blank" rel="noopener">Cetak Leger</a>
                </div>
            <?php endif ?>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr><th scope="col">No.</th><th scope="col">Nama siswa</th><th scope="col">NIS</th><th scope="col">Format rapor</th><th scope="col">Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($anggota as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <th scope="row">
                                <a href="<?= esc(url_to('detail-semester', $item['siswa_id'], $semester), 'attr') ?>"><?= esc($item['nama']) ?></a>
                            </th>
                            <td><?= esc($item['nis'] ?? '—') ?></td>
                            <td><?= esc($item['nama_format'] ?? '—') ?></td>
                            <td>
                                <?php if ($item['status_arsip'] === 'Ditemukan' && ! isset($tersedia[$item['id']])): ?>
                                    <span class="badge bg-label-warning">Format belum tersedia</span>
                                <?php else: ?>
                                    <?= view('operator/siswa/status_arsip', ['statusArsip' => $item['status_arsip']], ['saveData' => false]) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif ?>
<?= $this->endSection() ?>
