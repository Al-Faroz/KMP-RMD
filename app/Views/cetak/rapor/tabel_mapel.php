<?php
$kelompokKolom = false;
$daftarField = [];
foreach ($kolom as $field) {
    $kelompokKolom = $kelompokKolom || is_array($field);
    array_push($daftarField, ...(is_array($field) ? array_values($field) : [$field]));
}
$rentangBaris = $kelompokKolom ? 2 : 1;
$jumlahKolom = count($daftarField) + 2;
$khs = $khs ?? false;
$total = $total ?? [];
$khususDeskripsi = $khs && str_starts_with($daftarField[0] ?? '', 'deskripsi_');
?>
<table class="<?= $khususDeskripsi ? 'rapor-deskripsi' : 'rapor-tabel' ?><?= $padat ? ' rapor-khs1' : '' ?>" autosize="<?= $khs ? 1 : 1.08 ?>">
    <thead>
        <tr>
            <th rowspan="<?= $rentangBaris ?>"<?= $padat ? ' class="kolom-nomor"' : '' ?> style="width: <?= $lebarNo ?? 5 ?>%">No.</th>
            <th rowspan="<?= $rentangBaris ?>" style="width: <?= $lebarMapel ?>%">Mata Pelajaran</th>
            <?php foreach ($kolom as $label => $field): ?>
                <?php if (is_array($field)): ?>
                    <th colspan="<?= count($field) ?>"><?= esc($label) ?></th>
                <?php else: ?>
                    <th rowspan="<?= $rentangBaris ?>"<?= $khs && $field === 'sks' ? ' class="kolom-sks"' : '' ?><?= isset($lebarField[$field]) ? ' style="width: ' . $lebarField[$field] . '%"' : '' ?>><?= esc($label) ?></th>
                <?php endif ?>
            <?php endforeach ?>
        </tr>
        <?php if ($kelompokKolom): ?>
            <tr>
                <?php foreach ($kolom as $field): ?>
                    <?php if (is_array($field)): ?>
                        <?php foreach ($field as $label => $nama): ?>
                            <th class="kolom-sempit<?= $khs && $label === 'Predikat' ? ' kolom-predikat' : '' ?>"<?= isset($lebarField[$nama]) ? ' style="width: ' . $lebarField[$nama] . '%"' : '' ?>><?= esc($label) ?></th>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endforeach ?>
            </tr>
        <?php endif ?>
    </thead>
    <tbody>
        <?php
        $kelompok = $subkelompok = null;
        $nomor = 0;
        ?>
        <?php foreach ($mapel as $index => $item): ?>
            <?php if ($item['kelompok_mapel'] !== $kelompok): ?>
                <?php
                $subkelompok = null;
                $nomor = 0;
                ?>
                <?php if ($item['kelompok_mapel'] !== null): ?>
                    <tr class="judul-kelompok"><td colspan="<?= $jumlahKolom ?>" class="kelompok"><?= esc($item['kelompok_mapel']) ?></td></tr>
                <?php endif ?>
            <?php endif ?>
            <?php if ($item['subkelompok_mapel'] !== null && $item['subkelompok_mapel'] !== $subkelompok): ?>
                <?php $subjudul = $item['subkelompok_mapel']; ?>
                <?php if ($khs): ?>
                    <?php $subjudul = preg_replace('/\s*\*\)$/u', ' (bila ada)', $subjudul); ?>
                    <tr class="judul-kelompok"><td><?= ++$nomor ?></td><td colspan="<?= $jumlahKolom - 1 ?>" class="subkelompok"><?= esc($subjudul) ?></td></tr>
                <?php else: ?>
                    <tr class="judul-kelompok"><td colspan="<?= $jumlahKolom ?>" class="subkelompok"><?= esc($subjudul) ?></td></tr>
                <?php endif ?>
            <?php endif ?>
            <?php
            $kelompok = $item['kelompok_mapel'];
            $subkelompok = $item['subkelompok_mapel'];
            $noMapel = $khs ? ($subkelompok === null ? ++$nomor : '') : $index + 1;
            $baris = [];
            foreach ($daftarField as $field) {
                $isTeks = str_starts_with($field, 'deskripsi_') || $field === 'capaian_kompetensi';
                $baris[$field] = $isTeks ? $potongan($item[$field]) : [$tampil($item[$field], $field)];
            }
            $panjang = max(array_map('count', $baris));
            ?>
            <?php for ($bagian = 0; $bagian < $panjang; $bagian++): ?>
                <tr>
                    <td><?= $bagian === 0 ? $noMapel : '' ?></td>
                    <td class="teks"><?= esc($item['nama_mapel_asli']) ?></td>
                    <?php foreach ($baris as $field => $bagianIsi): ?>
                        <td<?= str_starts_with($field, 'deskripsi_') || $field === 'capaian_kompetensi'
                            ? ' class="teks' . ($khs && $padat && str_starts_with($field, 'deskripsi_') ? ' deskripsi-khs1' : '') . '"'
                            : '' ?>
                            <?= isset($lebarField[$field]) ? 'style="width: ' . $lebarField[$field] . '%"' : '' ?>>
                            <?= nl2br(esc($bagianIsi[$bagian] ?? ''), false) ?>
                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endfor ?>
        <?php endforeach ?>
        <?php if ($mapel === []): ?>
            <tr><td colspan="<?= $jumlahKolom ?>">Belum ada <?= $khususDeskripsi ? 'deskripsi' : 'nilai' ?> mata pelajaran.</td></tr>
        <?php endif ?>
        <?php if ($total !== []): ?>
            <tr class="total-khs">
                <th colspan="2" style="text-align: right">Jumlah</th>
                <?php foreach ($daftarField as $field): ?>
                    <th><?= esc($tampil($total[$field] ?? null)) ?></th>
                <?php endforeach ?>
            </tr>
        <?php endif ?>
    </tbody>
</table>
