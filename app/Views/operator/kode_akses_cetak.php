<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 col-xl-6">
        <?php if ($pesan = session()->getFlashdata('sukses')): ?>
            <div class="alert alert-success" role="status"><?= esc($pesan) ?></div>
        <?php endif ?>
        <?php if ($pesan = session()->getFlashdata('galat')): ?>
            <div class="alert alert-danger" role="alert"><?= esc($pesan) ?></div>
        <?php endif ?>
        <form class="card card-body" method="post" action="<?= esc(url_to('kode-akses-cetak'), 'attr') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="kode-cetak-operator" class="form-label"><?= $kodeAktif ? 'Kode Cetak Baru' : 'Kode Cetak' ?></label>
                <input id="kode-cetak-operator" name="kode_cetak" type="password" class="form-control"
                       autocomplete="new-password" minlength="8" maxlength="72" aria-describedby="petunjuk-kode-cetak"
                       <?= $kodeAktif ? '' : 'required' ?>>
                <div id="petunjuk-kode-cetak" class="form-text">
                    <?= $kodeAktif ? 'Kosongkan untuk mempertahankan kode yang aktif.' : 'Minimal 8 karakter. Kode berlaku sampai diganti.' ?>
                </div>
            </div>
            <div class="mb-4">
                <label for="whatsapp-operator" class="form-label">Nomor WhatsApp Operator</label>
                <input id="whatsapp-operator" name="whatsapp_operator" type="tel" class="form-control"
                       autocomplete="tel" value="<?= esc($whatsappOperator, 'attr') ?>" maxlength="32" required>
            </div>
            <div>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
