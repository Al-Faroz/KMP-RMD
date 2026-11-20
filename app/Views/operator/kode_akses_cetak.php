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
        <div class="card card-body mb-3">
            <?php if ($kode !== null): ?>
                <div class="mb-4">
                    <label for="kode-cetak-baru" class="form-label">Kode Cetak Aktif</label>
                    <div class="input-group">
                        <input id="kode-cetak-baru" type="text" class="form-control font-monospace"
                               value="<?= esc($kode, 'attr') ?>" readonly>
                        <button id="salin-kode-cetak" class="btn btn-outline-primary" type="button">Salin Kode</button>
                    </div>
                    <div id="status-salin-kode" class="form-text" role="status"></div>
                </div>
            <?php elseif ($galatDekripsi): ?>
                <p class="text-danger">Kode cetak gagal ditampilkan. Periksa kunci enkripsi server.</p>
            <?php elseif ($kodeAktif): ?>
                <p>Kode dari versi lama masih berlaku. Klik <strong>Buat Kode Baru</strong> sekali agar kode aktif bisa ditampilkan dan disalin lagi.</p>
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
        <form class="card card-body" method="post" action="<?= esc(url_to('simpan-whatsapp-cetak'), 'attr') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="whatsapp-operator" class="form-label">Nomor WhatsApp Operator</label>
                <input id="whatsapp-operator" name="whatsapp_operator" type="tel" class="form-control"
                       autocomplete="tel" value="<?= esc($whatsappOperator, 'attr') ?>" maxlength="32"
                       aria-describedby="petunjuk-whatsapp-operator" required>
                <div id="petunjuk-whatsapp-operator" class="form-text">Isi nomor aktif (08… atau +62…) untuk mengaktifkan tombol Pesan WhatsApp di modal cetak publik.</div>
            </div>
            <div>
                <button class="btn btn-primary" type="submit">Simpan Nomor</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
