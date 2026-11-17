<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<?php if ($sukses = session()->getFlashdata('sukses')): ?>
    <div class="alert alert-success" role="status"><?= esc($sukses) ?></div>
<?php endif ?>
<?php if ($galat = session()->getFlashdata('galat')): ?>
    <div class="alert alert-danger" role="alert"><?= esc($galat) ?></div>
<?php endif ?>

<div class="mb-3">
    <a class="btn btn-primary" href="<?= esc(url_to('tambah-operator'), 'attr') ?>">Tambah Operator</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th scope="col">Nama Lengkap</th>
                    <th scope="col">Username</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar as $akun): ?>
                    <tr>
                        <td class="text-break">
                            <?= esc($akun['nama_lengkap']) ?>
                            <?php if ((int) $akun['id'] === $idAktif): ?>
                                <span class="small text-muted d-block">Akun Anda</span>
                            <?php endif ?>
                        </td>
                        <td class="text-break"><?= esc($akun['username']) ?></td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="<?= esc(url_to('edit-operator', $akun['id']), 'attr') ?>">Edit</a>
                                <?php if ((int) $akun['id'] !== $idAktif): ?>
                                    <a class="btn btn-sm btn-outline-danger" href="<?= esc(url_to('hapus-operator', $akun['id']), 'attr') ?>">Hapus</a>
                                <?php endif ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
