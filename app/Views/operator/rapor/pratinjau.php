<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<a href="<?= esc(url_to('detail-semester', $siswa['id'], $semester), 'attr') ?>">Kembali ke Semester <?= esc($semester) ?></a>
<p class="mt-2 mb-4"><?= esc($siswa['nama']) ?> · Angkatan <?= esc($siswa['tahun_masuk']) ?></p>

<?php if ($perubahan === []): ?>
    <div class="alert alert-info" role="status">Tidak ada perubahan pada file ini.</div>
<?php else: ?>
    <section class="card mb-4">
        <div class="card-body"><h2 class="h5 mb-0"><?= count($perubahan) ?> perubahan</h2></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>Baris</th><th>Isian</th><th>Saat ini</th><th>Setelah impor</th></tr></thead>
                <tbody>
                    <?php foreach ($perubahan as $item): ?>
                        <tr>
                            <th scope="row" class="text-none"><?= esc($item['baris']) ?></th>
                            <td><?= esc($item['kolom']) ?></td>
                            <td class="text-break"><?= esc($item['lama'] ?? '—') ?></td>
                            <td class="text-break"><?= esc($item['baru'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
    <form method="post" action="<?= esc(url_to('konfirmasi-rapor', $siswa['id'], $semester), 'attr') ?>" class="d-flex gap-2 flex-wrap">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token, 'attr') ?>">
        <button class="btn btn-primary" type="submit">Konfirmasi Impor</button>
        <a class="btn btn-outline-secondary" href="<?= esc(url_to('detail-semester', $siswa['id'], $semester), 'attr') ?>">Batal</a>
    </form>
<?php endif ?>
<?= $this->endSection() ?>
