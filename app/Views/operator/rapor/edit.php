<?php use App\Libraries\PengesahanRapor; use App\Libraries\RaporFields; ?>

<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<?php
$rapor = is_array($masukan['rapor'] ?? null) ? $masukan['rapor'] : [];
$urlDetail = url_to('detail-semester', $siswa['id'], $semester);
?>
<a href="<?= esc($urlDetail, 'attr') ?>">Kembali ke Semester <?= esc($semester) ?></a>
<p class="mt-2 mb-4"><?= esc($siswa['nama']) ?> · Angkatan <?= esc($siswa['tahun_masuk']) ?></p>
<?php if ($galat !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= esc($galat) ?></div>
<?php endif ?>
<form action="<?= esc(url_to('edit-rapor', $siswa['id'], $semester), 'attr') ?>" method="post" id="form-edit-rapor">
    <?= csrf_field() ?>
    <input type="hidden" name="versi" value="<?= esc($versi, 'attr') ?>">
    <section class="card card-body mb-4">
        <h2 class="h5 mb-3">Rapor Semester</h2>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="kode-format" class="form-label">Format rapor</label>
                <select id="kode-format" name="rapor[kode_format]" class="form-select" required>
                    <option value="">Pilih format</option>
                    <?php foreach ($snapshot['format'] as $kode => $item): ?>
                        <option value="<?= esc($kode, 'attr') ?>" <?= $kode === ($rapor['kode_format'] ?? null) ? 'selected' : '' ?>><?= esc($item['nama_format']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="pengaturan-cetak" class="form-label">Rujukan arsip semester</label>
                <select id="pengaturan-cetak" name="rapor[pengaturan_cetak_id]" class="form-select">
                    <option value="">Pilih jika tersedia</option>
                    <?php foreach ($snapshot['pengaturan'] as $id => $item): ?>
                        <option value="<?= esc($id, 'attr') ?>" <?= (string) $id === (string) ($rapor['pengaturan_cetak_id'] ?? '') ? 'selected' : '' ?>>
                            <?= esc($item['kode_format'] . ' · ' . $item['tahun_pelajaran'] . ($item['rombel'] ? ' · ' . $item['rombel'] : '') . ' (#' . $id . ')') ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <?php $fields = array_diff_key(RaporFields::definitions('rapor'), ['kode_format' => true, 'pengaturan_cetak_id' => true]); ?>
            <?php foreach (array_intersect_key($fields, array_flip(['tahun_pelajaran', 'kelas', 'rombel', 'kurikulum'])) as $key => [$label, $type]): ?>
                <?= view('operator/rapor/field', ['name' => "rapor[{$key}]", 'label' => $label, 'type' => $type, 'value' => $rapor[$key] ?? ''], ['saveData' => false]) ?>
            <?php endforeach ?>
        </div>
        <details class="border rounded p-3">
            <summary class="fw-semibold">Ringkasan nilai</summary>
            <div class="row g-3 mt-1">
                <?php foreach (array_diff_key($fields, PengesahanRapor::WALI, array_flip(['tahun_pelajaran', 'kelas', 'rombel', 'kurikulum'])) as $key => [$label, $type]): ?>
                    <?= view('operator/rapor/field', ['name' => "rapor[{$key}]", 'label' => $label, 'type' => $type, 'value' => $rapor[$key] ?? ''], ['saveData' => false]) ?>
                <?php endforeach ?>
            </div>
        </details>
    </section>

    <?php foreach (RaporFields::TABLES as $bagian => $table): ?>
        <?php $rows = array_values(array_filter((array) ($masukan[$bagian] ?? []), 'is_array')); ?>
        <section class="card card-body mb-4" data-bagian-rapor data-next-index="<?= count($rows) ?>">
            <h2 class="h5 mb-3"><?= esc(RaporFields::LABELS[$bagian]) ?></h2>
            <?php if ($bagian === 'pendukung'): ?>
                <details class="border rounded p-3 mb-3"<?= $galat !== '' ? ' open' : '' ?>>
                    <summary class="fw-semibold">Pengesahan arsip</summary>
                    <p id="pengesahan-arsip" class="mt-2 mb-0">Ubah hanya untuk mencocokkan isian dengan dokumen asli semester ini.</p>
                    <?php $kepala = (array) ($masukan['pengesahan'] ?? []) + $snapshot['pengesahan']; ?>
                    <div class="row g-3 mt-1">
                        <?php foreach (PengesahanRapor::KEPALA as $key => [$label, $type]): ?>
                            <?= view('operator/rapor/field', ['name' => "pengesahan[{$key}]", 'label' => $label, 'type' => $type, 'value' => $kepala[$key] ?? ''], ['saveData' => false]) ?>
                        <?php endforeach ?>
                        <?php foreach (PengesahanRapor::WALI as $key => [$label, $type]): ?>
                            <?= view('operator/rapor/field', ['name' => "rapor[{$key}]", 'label' => $label, 'type' => $type, 'value' => $rapor[$key] ?? ''], ['saveData' => false]) ?>
                        <?php endforeach ?>
                    </div>
                </details>
            <?php endif ?>
            <div data-daftar-baris>
                <?php foreach ($rows as $index => $row): ?>
                    <?= view('operator/rapor/baris', compact('bagian', 'index', 'row', 'galat'), ['saveData' => false]) ?>
                <?php endforeach ?>
            </div>
            <template data-template-baris>
                <?= view('operator/rapor/baris', ['bagian' => $bagian, 'index' => '__index__', 'row' => []], ['saveData' => false]) ?>
            </template>
            <div class="mt-2">
                <button type="button" class="btn btn-outline-secondary" data-tambah-baris><?= $bagian === 'nilai' ? 'Tambah Mata Pelajaran' : 'Tambah Data Pendukung' ?></button>
            </div>
        </section>
    <?php endforeach ?>
    <input type="hidden" name="form_lengkap" value="1">
    <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">Simpan Rapor</button>
        <a href="<?= esc($urlDetail, 'attr') ?>" class="btn btn-outline-secondary">Batal</a>
    </div>
</form>
<?php if (($snapshot['rapor']['status_arsip'] ?? null) !== 'Ditemukan' && $snapshot['format'] !== []): ?>
    <form method="get" action="<?= esc(url_to('excel-rapor', $siswa['id'], $semester), 'attr') ?>"
          class="row g-2 align-items-end mt-4">
        <div class="col-md-auto">
            <label for="format-template" class="form-label">Format template Excel</label>
            <select id="format-template" name="format" class="form-select" required>
                <option value="">Pilih format</option>
                <?php foreach ($snapshot['format'] as $kode => $item): ?>
                    <option value="<?= esc($kode, 'attr') ?>"><?= esc($item['nama_format']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-outline-primary">Unduh Template Excel</button>
        </div>
    </form>
<?php endif ?>
<?= $this->endSection() ?>
