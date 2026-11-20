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
            <?php if ($kodeUniversal !== ''): ?>
                <label for="kode-cetak-universal" class="form-label">Kode Universal Operator</label>
                <div class="input-group">
                    <input id="kode-cetak-universal" type="text" class="form-control font-monospace"
                           value="<?= esc($kodeUniversal, 'attr') ?>" readonly>
                    <button class="btn btn-outline-primary" type="button" data-salin-kode="kode-cetak-universal"
                            aria-describedby="status-salin-universal">Salin Kode Universal</button>
                </div>
                <div id="status-salin-universal" class="form-text" role="status"></div>
                <p class="form-text mb-0">Selalu aktif untuk cetak oleh operator.</p>
            <?php else: ?>
                <p class="mb-0">Kode Universal Operator belum dikonfigurasi di server.</p>
            <?php endif ?>
        </div>
        <div class="card card-body mb-3">
            <h2 class="h6 mb-3">Kode Sementara Pengguna</h2>
            <?php if ($kodeSementara !== null): ?>
                <div class="mb-3">
                    <label for="kode-cetak-sementara" class="form-label">Kode Sementara Aktif</label>
                    <div class="input-group">
                        <input id="kode-cetak-sementara" type="text" class="form-control font-monospace"
                               value="<?= esc($kodeSementara, 'attr') ?>" readonly>
                        <button class="btn btn-outline-primary" type="button" data-salin-kode="kode-cetak-sementara"
                                aria-describedby="status-salin-sementara">Salin Kode Sementara</button>
                    </div>
                    <div id="status-salin-sementara" class="form-text" role="status"></div>
                </div>
            <?php elseif ($galatDekripsi): ?>
                <p class="text-danger">Kode sementara gagal ditampilkan. Periksa kunci enkripsi server.</p>
            <?php elseif ($kodeSementaraAktif): ?>
                <p>Kode dari versi lama masih berlaku. Buat kode sementara baru agar bisa langsung disalin di sini.</p>
            <?php endif ?>
            <?php if ($akhirBerlaku !== null): ?>
                <p class="form-text mb-2"><?= $kodeSementaraAktif ? 'Berlaku sampai' : 'Kedaluwarsa pada' ?> <?= esc($akhirBerlaku) ?>.</p>
            <?php endif ?>
            <form method="post" action="<?= esc(url_to('kode-akses-cetak'), 'attr') ?>">
                <?= csrf_field() ?>
                <p><?= $kodeSementaraAktif ? 'Membuat kode baru langsung mengganti kode sementara yang aktif.' : 'Kode baru berlaku 24 jam dan dapat dibagikan kepada pengguna.' ?></p>
                <button class="btn btn-primary" type="submit"><?= $kodeSementaraAktif ? 'Buat Kode Sementara Baru' : 'Buat Kode Sementara' ?></button>
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
