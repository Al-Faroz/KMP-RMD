<?php
$pesanWhatsApp = <<<'PESAN'
Halo, Operator Madrasah. Saya ingin meminta kode akses untuk mencetak rapor atau dokumen siswa.

Nama pemohon: [isi nama Anda]
Nama siswa: [isi nama siswa]
Angkatan: [isi angkatan]
Semester/dokumen: [isi yang ingin dicetak]

Terima kasih.
PESAN;
?>
<div class="modal fade" id="modal-kode-cetak" tabindex="-1" aria-labelledby="judul-kode-cetak">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="judul-kode-cetak" class="modal-title h5">Kode Akses Cetak</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="form-kode-cetak" method="post">
                <div class="modal-body">
                    <label for="kode-cetak" class="form-label">Kode Cetak</label>
                    <input id="kode-cetak" name="kode_cetak" type="password" class="form-control"
                           autocomplete="off" maxlength="72" required>
                    <p id="pesan-kode-cetak" class="text-danger small mt-2 mb-0" role="alert" hidden></p>
                    <?php if ($whatsappOperator === ''): ?>
                        <p id="status-whatsapp-cetak" class="mt-3 mb-2">Nomor WhatsApp operator belum diatur.</p>
                        <button type="button" class="btn btn-outline-success"
                                aria-describedby="status-whatsapp-cetak" disabled>Pesan WhatsApp</button>
                    <?php else: ?>
                        <p class="mt-3 mb-2">Hubungi operator madrasah untuk mendapatkan kode.</p>
                        <a class="btn btn-outline-success"
                           href="<?= esc('https://wa.me/' . $whatsappOperator . '?text=' . rawurlencode($pesanWhatsApp), 'attr') ?>"
                           target="_blank" rel="noopener noreferrer">Pesan WhatsApp</a>
                    <?php endif ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cetak PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>
