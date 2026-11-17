<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<?php
$edit = isset($akun['id']);
$tujuan = $edit ? url_to('edit-operator', $akun['id']) : url_to('tambah-operator');
?>
<div class="row">
    <div class="col-md-8 col-xl-6">
        <?php if ($galat !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= esc($galat) ?></div>
        <?php endif ?>
        <form class="card card-body" method="post" action="<?= esc($tujuan, 'attr') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label" for="nama-operator">Nama Lengkap</label>
                <input class="form-control" id="nama-operator" name="nama_lengkap" type="text" autocomplete="name"
                       value="<?= esc($akun['nama_lengkap'], 'attr') ?>" maxlength="255" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="username-operator">Username</label>
                <input class="form-control" id="username-operator" name="username" type="text" autocomplete="username"
                       value="<?= esc($akun['username'], 'attr') ?>" maxlength="64" autocapitalize="none" spellcheck="false" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password-operator">Kata Sandi<?= $edit ? ' Baru' : '' ?></label>
                <input class="form-control" id="password-operator" name="password" type="password" autocomplete="new-password"
                       minlength="8" aria-describedby="petunjuk-sandi" <?= $edit ? '' : 'required' ?>>
                <div class="form-text" id="petunjuk-sandi">
                    <?= $edit ? 'Kosongkan untuk mempertahankan kata sandi saat ini.' : 'Minimal 8 karakter.' ?>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-outline-secondary" href="<?= esc(url_to('daftar-operator'), 'attr') ?>">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
