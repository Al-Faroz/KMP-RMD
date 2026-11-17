<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 col-xl-6">
        <form class="card card-body" method="post" action="<?= esc(url_to('hapus-operator', $akun['id']), 'attr') ?>">
            <?= csrf_field() ?>
            <p class="mb-4">Hapus akun <strong><?= esc($akun['nama_lengkap']) ?></strong> (<?= esc($akun['username']) ?>)? Akun ini akan kehilangan akses ke RMD.</p>
            <div class="d-flex gap-2">
                <button class="btn btn-danger" type="submit">Hapus Akun</button>
                <a class="btn btn-outline-secondary" href="<?= esc(url_to('daftar-operator'), 'attr') ?>">Batal</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
