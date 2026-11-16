<div class="tetap-bersama">
    <h2>Ekstrakurikuler</h2>
    <table class="rapor-tabel">
        <thead><tr><th style="width: 6%">No.</th><th style="width: 34%">Kegiatan</th><th style="width: 17%">Nilai / Predikat</th><th>Keterangan</th></tr></thead>
        <tbody>
            <?php $ekstrakurikuler = $pendukung['ekstrakurikuler'] ?? []; ?>
            <?php foreach ($ekstrakurikuler as $i => $item): ?>
                <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $bagian => $isi): ?>
                    <tr<?= trim($isi) === '' ? ' class="isian-manual"' : '' ?>>
                        <td><?= $bagian > 0 ? '' : $i + 1 ?></td><td class="teks"><?= esc($item['nama_item'] ?? '') ?></td>
                        <td><?= esc($bagian === 0 ? trim($tampil($item['nilai'] ?? null) . ' ' . ($item['predikat'] ?? '')) : '') ?></td>
                        <td class="teks"><?= nl2br(esc($isi), false) ?></td>
                    </tr>
                <?php endforeach ?>
            <?php endforeach ?>
            <?php for ($i = count($ekstrakurikuler); $i < 2; $i++): ?>
                <tr class="isian-manual"><td><?= $i + 1 ?></td><td></td><td></td><td></td></tr>
            <?php endfor ?>
        </tbody>
    </table>

    <h2>Prestasi</h2>
    <table class="rapor-tabel">
        <thead><tr><th style="width: 6%">No.</th><th style="width: 39%">Jenis Prestasi</th><th>Keterangan</th></tr></thead>
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

    <h2>Ketidakhadiran</h2>
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

    <?php foreach (['Catatan Wali Kelas', 'Tanggapan Orang Tua / Wali'] as $judulCatatan): ?>
        <?php
        $tanggapan = $judulCatatan === 'Tanggapan Orang Tua / Wali';
        $kunciCatatan = $tanggapan ? 'tanggapan orang tua/wali' : 'catatan wali kelas';
        $catatan = array_filter(array_map(
            static fn (array $item): string => trim($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? ''),
            $pendukung[$kunciCatatan] ?? []
        ), static fn (string $isi): bool => $isi !== '');
        ?>
        <h2><?= esc($judulCatatan) ?></h2>
        <?php if ($catatan === []): ?>
            <table class="kotak-isian"><tr><td<?= $tanggapan ? ' class="tanggapan"' : '' ?>>&nbsp;</td></tr></table>
        <?php else: ?>
            <div class="bingkai"><?= nl2br(esc(implode("\n", $catatan)), false) ?></div>
        <?php endif ?>
    <?php endforeach ?>

    <table class="ttd">
        <tr><td style="width: 57%"></td><td><?= esc($tempatTanggal) ?></td></tr>
        <tr><td>Orang Tua / Wali,</td><td><?= esc($rapor['jabatan_cetak'] ?? 'Wali Kelas') ?>,</td></tr>
        <tr><td class="ttd-nama">........................................</td><td class="ttd-nama"><?= esc($rapor['nama_penandatangan'] ?? '........................................') ?></td></tr>
        <tr><td></td><td>NIP <?= esc($rapor['nip_penandatangan'] ?? '........................................') ?></td></tr>
        <tr><td colspan="2" class="kepala"><?= esc($pengaturan['jabatan_kepala_cetak'] ?? 'Kepala Madrasah') ?>,</td></tr>
        <tr><td colspan="2" class="ttd-nama" style="text-align: center"><?= esc($pengaturan['nama_kepala_madrasah'] ?? '........................................') ?></td></tr>
        <tr><td colspan="2" style="text-align: center">NIP <?= esc($pengaturan['nip_kepala_madrasah'] ?? '........................................') ?></td></tr>
    </table>
</div>
