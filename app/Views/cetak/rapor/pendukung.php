<h2>Ekstrakurikuler</h2>
<table class="rapor-tabel">
    <thead><tr><th style="width: 6%">No.</th><th style="width: 34%">Kegiatan</th><th style="width: 17%">Nilai / Predikat</th><th>Keterangan</th></tr></thead>
    <tbody>
        <?php foreach ($pendukung['ekstrakurikuler'] ?? [[]] as $i => $item): ?>
            <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $bagian => $isi): ?>
                <tr>
                    <td><?= $item === [] || $bagian > 0 ? '' : $i + 1 ?></td><td class="teks"><?= esc($item['nama_item'] ?? '') ?></td>
                    <td><?= esc($bagian === 0 ? trim($tampil($item['nilai'] ?? null) . ' ' . ($item['predikat'] ?? '')) : '') ?></td>
                    <td class="teks"><?= nl2br(esc($isi), false) ?></td>
                </tr>
            <?php endforeach ?>
        <?php endforeach ?>
    </tbody>
</table>

<h2>Prestasi</h2>
<table class="rapor-tabel">
    <thead><tr><th style="width: 6%">No.</th><th style="width: 39%">Jenis Prestasi</th><th>Keterangan</th></tr></thead>
    <tbody>
        <?php foreach ($pendukung['prestasi'] ?? [[]] as $i => $item): ?>
            <?php foreach ($potongan($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? null) as $bagian => $isi): ?>
                <tr>
                    <td><?= $item === [] || $bagian > 0 ? '' : $i + 1 ?></td>
                    <td class="teks"><?= esc($item['nama_item'] ?? '') ?></td><td class="teks"><?= nl2br(esc($isi), false) ?></td>
                </tr>
            <?php endforeach ?>
        <?php endforeach ?>
    </tbody>
</table>

<h2>Ketidakhadiran</h2>
<table class="rapor-tabel" style="width: 55%">
    <tbody>
        <?php foreach ($pendukung['ketidakhadiran'] ?? array_map(static fn ($nama) => ['nama_item' => $nama], ['Sakit', 'Izin', 'Tanpa Keterangan']) as $item): ?>
            <tr>
                <td class="teks" style="width: 63%"><?= esc($item['nama_item'] ?? '') ?></td>
                <td><?= esc($item['isian_tercetak_asli'] ?? trim($tampil($item['jumlah'] ?? null) . ' ' . ($item['satuan'] ?? ''))) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<h2>Catatan Wali Kelas</h2>
<div class="bingkai">
    <?php foreach ($pendukung['catatan wali kelas'] ?? [] as $item): ?>
        <div><?= nl2br(esc($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? ''), false) ?></div>
    <?php endforeach ?>
</div>
<h2>Tanggapan Orang Tua / Wali</h2>
<div class="bingkai">
    <?php foreach ($pendukung['tanggapan orang tua/wali'] ?? [] as $item): ?>
        <div><?= nl2br(esc($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? ''), false) ?></div>
    <?php endforeach ?>
</div>

<table class="ttd">
    <tr><td style="width: 57%"></td><td><?= esc($tempatTanggal) ?></td></tr>
    <tr><td>Orang Tua / Wali,</td><td><?= esc($rapor['jabatan_cetak'] ?? 'Wali Kelas') ?>,</td></tr>
    <tr><td class="ttd-nama">........................................</td><td class="ttd-nama"><?= esc($rapor['nama_penandatangan'] ?? '........................................') ?></td></tr>
    <tr><td></td><td>NIP <?= esc($rapor['nip_penandatangan'] ?? '........................................') ?></td></tr>
    <tr><td colspan="2" class="kepala"><?= esc($pengaturan['jabatan_kepala_cetak'] ?? 'Kepala Madrasah') ?>,</td></tr>
    <tr><td colspan="2" class="ttd-nama" style="text-align: center"><?= esc($pengaturan['nama_kepala_madrasah'] ?? '........................................') ?></td></tr>
    <tr><td colspan="2" style="text-align: center">NIP <?= esc($pengaturan['nip_kepala_madrasah'] ?? '........................................') ?></td></tr>
</table>
