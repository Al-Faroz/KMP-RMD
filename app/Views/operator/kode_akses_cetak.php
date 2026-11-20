<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 col-xl-6">
        <?php if ($pesan = session()->getFlashdata('galat')): ?>
            <div class="alert alert-danger" role="alert"><?= esc($pesan) ?></div>
        <?php endif ?>
        <div class="card card-body">
            <?php if ($kodeBaru !== null): ?>
                <div class="mb-4">
                    <label for="kode-cetak-baru" class="form-label">Kode Cetak Aktif</label>
                    <div class="input-group">
                        <input id="kode-cetak-baru" type="text" class="form-control font-monospace"
                               value="<?= esc($kodeBaru, 'attr') ?>" readonly>
                        <button id="salin-kode-cetak" class="btn btn-outline-primary" type="button">Salin Kode</button>
                    </div>
                    <div id="status-salin-kode" class="form-text" role="status">Salin kode sebelum meninggalkan halaman.</div>
                </div>
            <?php endif ?>
            <form method="post" action="<?= esc(url_to('kode-akses-cetak'), 'attr') ?>">
                <?= csrf_field() ?>
                <?php if ($kodeAktif): ?>
                    <p>Kode yang aktif akan langsung diganti saat kode baru dibuat.</p>
                <?php else: ?>
                    <p>Buat kode untuk mengaktifkan cetak publik.</p>
                <?php endif ?>
                <button class="btn btn-primary" type="submit"><?= $kodeAktif ? 'Buat Kode Baru' : 'Buat Kode' ?></button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
