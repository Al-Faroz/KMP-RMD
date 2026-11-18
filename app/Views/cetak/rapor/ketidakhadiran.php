<h2><?= esc($judul) ?></h2>
<table class="rapor-tabel" style="width: 55%">
    <tbody>
        <?php foreach ($pendukung['ketidakhadiran'] ?? array_map(static fn ($nama) => ['nama_item' => $nama], ['Sakit', 'Izin', 'Tanpa Keterangan']) as $item): ?>
            <?php
            $arsip = trim($item['isian_tercetak_asli'] ?? '');
            $jumlah = $item['jumlah'] ?? null;
            $isi = $arsip !== '' ? $arsip : trim($tampil($jumlah) . ' ' . ($item['satuan'] ?? ''));
            ?>
            <tr<?= $arsip === '' && $jumlah === null ? ' class="isian-manual"' : '' ?>>
                <td class="teks" style="width: 63%"><?= esc($item['nama_item'] ?? '') ?></td>
                <td><?= esc($isi) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
