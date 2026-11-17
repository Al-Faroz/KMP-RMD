<?php use App\Models\SiswaModel; ?>

<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<a href="<?= esc(url_to('detail-siswa', $siswa['id']), 'attr') ?>">Kembali ke Detail Siswa</a>
<p class="mt-2 mb-4"><?= esc($siswa['nama']) ?> · Angkatan <?= esc($siswa['tahun_masuk']) ?></p>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger" role="alert"><?= esc($errors['form'] ?? 'Periksa kembali isian yang ditandai.') ?></div>
<?php endif ?>

<form method="post" action="<?= esc(url_to('edit-siswa', $siswa['id']), 'attr') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="versi" value="<?= esc($versi, 'attr') ?>">
    <?php foreach (SiswaModel::IDENTITAS as $judul => $fields): ?>
        <fieldset class="card card-body mb-4">
            <legend class="h5 mb-3"><?= esc($judul) ?></legend>
            <div class="row g-3">
                <?php foreach ($fields as $field => [$label, $type]): ?>
                    <?php
                    $id = 'identitas-' . $field;
                    $value = $identitas[$field] ?? '';
                    $error = $errors[$field] ?? null;
                    $invalid = $error === null ? '' : ' is-invalid';
                    $feedback = $error === null ? '' : ' aria-invalid="true" aria-describedby="' . esc($id . '-error', 'attr') . '"';
                    ?>
                    <div class="<?= $type === 'textarea' ? 'col-12' : 'col-sm-6 col-lg-4' ?>">
                        <label class="form-label" for="<?= esc($id, 'attr') ?>"><?= esc($label) ?></label>
                        <?php if ($type === 'textarea'): ?>
                            <textarea class="form-control<?= $invalid ?>" id="<?= esc($id, 'attr') ?>"
                                      name="<?= esc($field, 'attr') ?>" rows="2" maxlength="16000"<?= $feedback ?>><?= esc($value) ?></textarea>
                        <?php else: ?>
                            <input class="form-control<?= $invalid ?>" id="<?= esc($id, 'attr') ?>"
                                   name="<?= esc($field, 'attr') ?>" type="<?= esc($type, 'attr') ?>"
                                   value="<?= esc($value, 'attr') ?>"<?= $feedback ?>
                                   <?= $type === 'number' ? 'min="0" max="2147483647"' : '' ?>
                                   <?= in_array($type, ['text', 'tel'], true) ? 'maxlength="255"' : '' ?>
                                   <?= $field === 'nama' ? 'required' : '' ?>>
                        <?php endif ?>
                        <?php if ($error !== null): ?>
                            <div class="invalid-feedback" id="<?= esc($id . '-error', 'attr') ?>"><?= esc($error) ?></div>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            </div>
        </fieldset>
    <?php endforeach ?>
    <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-primary" type="submit">Simpan Identitas</button>
        <a class="btn btn-outline-secondary" href="<?= esc(url_to('detail-siswa', $siswa['id']), 'attr') ?>">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
