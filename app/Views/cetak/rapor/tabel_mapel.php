<?php
$kelompokKolom = false;
$daftarField = [];
foreach ($kolom as $field) {
    $kelompokKolom = $kelompokKolom || is_array($field);
    array_push($daftarField, ...(is_array($field) ? array_values($field) : [$field]));
}
$rentangBaris = $kelompokKolom ? 2 : 1;
$jumlahKolom = count($daftarField) + 2;
?>
<table class="rapor-tabel" autosize="1.08">
    <thead>
        <tr>
            <th rowspan="<?= $rentangBaris ?>" style="width: 5%">No.</th>
            <th rowspan="<?= $rentangBaris ?>" style="width: <?= $lebarMapel ?>%">Mata Pelajaran</th>
            <?php foreach ($kolom as $label => $field): ?>
                <?php if (is_array($field)): ?>
                    <th colspan="<?= count($field) ?>"><?= esc($label) ?></th>
                <?php else: ?>
                    <th rowspan="<?= $rentangBaris ?>"<?= isset($lebarField[$field]) ? ' style="width: ' . $lebarField[$field] . '%"' : '' ?>><?= esc($label) ?></th>
                <?php endif ?>
            <?php endforeach ?>
        </tr>
        <?php if ($kelompokKolom): ?>
            <tr>
                <?php foreach ($kolom as $field): ?>
                    <?php if (is_array($field)): ?>
                        <?php foreach ($field as $label => $nama): ?>
                            <th class="kolom-sempit"<?= isset($lebarField[$nama]) ? ' style="width: ' . $lebarField[$nama] . '%"' : '' ?>><?= esc($label) ?></th>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endforeach ?>
            </tr>
        <?php endif ?>
    </thead>
    <tbody>
        <?php $kelompok = $subkelompok = null; ?>
        <?php foreach ($mapel as $index => $item): ?>
            <?php if ($item['kelompok_mapel'] !== $kelompok): ?>
                <?php $subkelompok = null; ?>
                <?php if ($item['kelompok_mapel'] !== null): ?>
                    <tr><td colspan="<?= $jumlahKolom ?>" class="kelompok"><?= esc($item['kelompok_mapel']) ?></td></tr>
                <?php endif ?>
            <?php endif ?>
            <?php if ($item['subkelompok_mapel'] !== null && $item['subkelompok_mapel'] !== $subkelompok): ?>
                <tr><td colspan="<?= $jumlahKolom ?>" class="subkelompok"><?= esc($item['subkelompok_mapel']) ?></td></tr>
            <?php endif ?>
            <?php
            $kelompok = $item['kelompok_mapel'];
            $subkelompok = $item['subkelompok_mapel'];
            $baris = [];
            foreach ($daftarField as $field) {
                $isTeks = str_starts_with($field, 'deskripsi_') || $field === 'capaian_kompetensi';
                $baris[$field] = $isTeks ? $potongan($item[$field]) : [$tampil($item[$field])];
            }
            $panjang = max(array_map('count', $baris));
            ?>
            <?php for ($bagian = 0; $bagian < $panjang; $bagian++): ?>
                <tr>
                    <td><?= $bagian === 0 ? $index + 1 : '' ?></td>
                    <td class="teks"><?= esc($item['nama_mapel_asli']) ?></td>
                    <?php foreach ($baris as $field => $bagianIsi): ?>
                        <td<?= str_starts_with($field, 'deskripsi_') || $field === 'capaian_kompetensi' ? ' class="teks"' : '' ?>
                            <?= isset($lebarField[$field]) ? 'style="width: ' . $lebarField[$field] . '%"' : '' ?>>
                            <?= nl2br(esc($bagianIsi[$bagian] ?? ''), false) ?>
                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endfor ?>
        <?php endforeach ?>
        <?php if ($mapel === []): ?>
            <tr><td colspan="<?= $jumlahKolom ?>">Belum ada nilai mata pelajaran.</td></tr>
        <?php endif ?>
    </tbody>
</table>
