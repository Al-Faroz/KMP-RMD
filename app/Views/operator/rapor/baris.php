<?php
use App\Libraries\RaporFields;

$namaBaris = $row[RaporFields::REQUIRED[$bagian]] ?? null;
?>
<details class="border rounded p-3 mb-2" data-baris-rapor<?= !empty($galat) ? ' open' : '' ?>>
    <summary class="fw-semibold"><?= esc(is_scalar($namaBaris) && $namaBaris !== '' ? $namaBaris : 'Baris baru') ?></summary>
    <input type="hidden" name="<?= esc($bagian . '[' . $index . '][id]', 'attr') ?>" value="<?= esc(is_scalar($row['id'] ?? null) ? $row['id'] : '', 'attr') ?>">
    <div class="row g-3 mt-1">
        <?php foreach (RaporFields::definitions($bagian) as $key => [$label, $type]): ?>
            <?= view('operator/rapor/field', [
                'name' => $bagian . '[' . $index . '][' . $key . ']',
                'value' => $row[$key] ?? '',
                'label' => $label,
                'type' => $type,
            ], ['saveData' => false]) ?>
        <?php endforeach ?>
    </div>
    <?php if (empty($row['id'])): ?>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-3" data-hapus-baris>Hapus baris baru</button>
    <?php endif ?>
</details>
