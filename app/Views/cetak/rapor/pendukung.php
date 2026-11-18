<?php
$khs = isset($khs1);
$khs1 = $khs1 ?? false;
?>
<div<?= $khs ? '' : ' class="tetap-bersama"' ?>>
    <h2><?= $khs1 ? 'C. ' : ($khs ? 'D. ' : '') ?>Ekstrakurikuler</h2>
    <table class="rapor-tabel">
        <thead>
            <tr>
                <th style="width: 6%">No.</th><th style="width: 34%"><?= $khs ? 'Kegiatan Ekstrakurikuler' : 'Kegiatan' ?></th>
                <?php if (! $khs1): ?><th style="width: 17%"><?= $khs ? 'Predikat' : 'Nilai / Predikat' ?></th><?php endif ?>
                <th><?= $khs && ! $khs1 ? 'Deskripsi' : 'Keterangan' ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $ekstrakurikuler = $pendukung['ekstrakurikuler'] ?? []; ?>
            <?php foreach ($ekstrakurikuler as $i => $item): ?>
                <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $bagian => $isi): ?>
                    <tr<?= trim($isi) === '' ? ' class="isian-manual"' : '' ?>>
                        <td><?= $bagian > 0 ? '' : $i + 1 ?></td><td class="teks"><?= esc($item['nama_item'] ?? '') ?></td>
                        <?php if (! $khs1): ?>
                            <?php $nilai = $khs ? ($item['predikat'] ?? $tampil($item['nilai'] ?? null)) : trim($tampil($item['nilai'] ?? null) . ' ' . ($item['predikat'] ?? '')); ?>
                            <td><?= esc($bagian === 0 ? $nilai : '') ?></td>
                        <?php endif ?>
                        <td class="teks"><?= nl2br(esc($isi), false) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endforeach ?>
            <?php for ($i = count($ekstrakurikuler); $i < ($khs ? 3 : 2); $i++): ?>
                <tr class="isian-manual"><td><?= $i + 1 ?></td><td></td><?php if (! $khs1): ?><td></td><?php endif ?><td></td></tr>
            <?php endfor ?>
        </tbody>
    </table>

    <?php if ($khs1): ?>
        <?= view('cetak/rapor/ketidakhadiran', compact('pendukung', 'tampil') + ['judul' => 'D. Ketidakhadiran'], ['saveData' => false]) ?>
    <?php endif ?>

    <h2><?= $khs ? 'E. ' : '' ?>Prestasi</h2>
    <table class="rapor-tabel">
        <thead><tr><th style="width: 6%">No.</th><th style="width: 39%"><?= $khs && ! $khs1 ? 'Jenis Kegiatan' : 'Jenis Prestasi' ?></th><th><?= $khs && ! $khs1 ? 'Deskripsi' : 'Keterangan' ?></th></tr></thead>
        <tbody>
            <?php $prestasi = $pendukung['prestasi'] ?? []; ?>
            <?php foreach ($prestasi as $i => $item): ?>
                <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $bagian => $isi): ?>
                    <tr<?= trim($isi) === '' ? ' class="isian-manual"' : '' ?>>
                        <td><?= $bagian > 0 ? '' : $i + 1 ?></td>
                        <td class="teks"><?= esc($item['nama_item'] ?? '') ?></td><td class="teks"><?= nl2br(esc($isi), false) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endforeach ?>
            <?php for ($i = count($prestasi); $i < 3; $i++): ?>
                <tr class="isian-manual"><td><?= $i + 1 ?></td><td></td><td></td></tr>
            <?php endfor ?>
        </tbody>
    </table>

    <?php if (! $khs1): ?>
        <?= view('cetak/rapor/ketidakhadiran', compact('pendukung', 'tampil') + ['judul' => $khs ? 'F. Ketidakhadiran' : 'Ketidakhadiran'], ['saveData' => false]) ?>
    <?php endif ?>

    <?php foreach (['Catatan Wali Kelas', 'Tanggapan Orang Tua / Wali'] as $judulCatatan): ?>
        <?php
        $tanggapan = $judulCatatan === 'Tanggapan Orang Tua / Wali';
        $kunciCatatan = $tanggapan ? 'tanggapan orang tua/wali' : 'catatan wali kelas';
        $catatan = array_filter(array_map(
            static fn (array $item): string => trim($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? ''),
            $pendukung[$kunciCatatan] ?? ($tanggapan ? ($pendukung['tanggapan orang tua'] ?? []) : [])
        ), static fn (string $isi): bool => $isi !== '');
        ?>
        <?php $huruf = $khs1 ? ($tanggapan ? 'G. ' : 'F. ') : ($khs ? ($tanggapan ? 'H. ' : 'G. ') : ''); ?>
        <h2><?= esc($huruf . $judulCatatan) ?></h2>
        <?php if ($khs || $catatan === []): ?>
            <table class="kotak-isian"><tr><td<?= $tanggapan ? ' class="tanggapan"' : '' ?>><?= $catatan === [] ? '&nbsp;' : nl2br(esc(implode("\n", $catatan)), false) ?></td></tr></table>
        <?php else: ?>
            <div class="bingkai"><?= nl2br(esc(implode("\n", $catatan)), false) ?></div>
        <?php endif ?>
    <?php endforeach ?>

    <table class="ttd">
        <tr><td style="width: 57%"></td><td><?= esc($pengesahan['tempat_tanggal_cetak_asli'] ?? '') ?></td></tr>
        <tr><td>Orang Tua / Wali,</td><td><?= esc($pengesahan['jabatan_cetak'] ?? '') ?></td></tr>
        <tr><td class="ttd-nama">........................................</td><td class="ttd-nama"><?= esc($pengesahan['nama_penandatangan'] ?? '........................................') ?></td></tr>
        <tr><td></td><td>NIP <?= esc($pengesahan['nip_penandatangan'] ?? '........................................') ?></td></tr>
        <tr><td colspan="2" class="kepala"><?= esc($pengesahan['jabatan_kepala_cetak'] ?? '') ?></td></tr>
        <tr><td colspan="2" class="ttd-nama" style="text-align: center"><?= esc($pengesahan['nama_kepala_madrasah'] ?? '........................................') ?></td></tr>
        <tr><td colspan="2" style="text-align: center">NIP <?= esc($pengesahan['nip_kepala_madrasah'] ?? '........................................') ?></td></tr>
    </table>
</div>
