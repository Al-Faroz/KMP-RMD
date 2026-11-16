<?php
use App\Libraries\RaporFields;

$idInput = str_replace(['[', ']'], ['-', ''], $name);
$value = is_scalar($value) ? (string) $value : '';
if ($type === 'number' && $value !== '') {
    $value = RaporFields::number($value);
}
?>
<div class="<?= $type === 'text' ? 'col-12' : 'col-sm-6 col-xl-4' ?>">
    <label class="form-label" for="<?= esc($idInput, 'attr') ?>"><?= esc($label) ?></label>
    <?php if ($type === 'text'): ?>
        <textarea class="form-control" rows="2" maxlength="16000" id="<?= esc($idInput, 'attr') ?>"
                  name="<?= esc($name, 'attr') ?>"><?= esc($value) ?></textarea>
    <?php else: ?>
        <input class="form-control" id="<?= esc($idInput, 'attr') ?>"
               name="<?= esc($name, 'attr') ?>" value="<?= esc($value, 'attr') ?>"
               <?= in_array($type, ['number', 'integer'], true) ? 'inputmode="decimal"' : '' ?>
               maxlength="<?= $type === 'string' ? 255 : 32 ?>">
    <?php endif ?>
</div>
