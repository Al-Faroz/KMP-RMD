<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>
<?php
$status = $rapor['status_arsip'] ?? 'Belum ditemukan';
$angka = static function ($value): string {
    if ($value === null) {
        return '—';
    }

    $text = (string) $value;

    return str_contains($text, '.') ? rtrim(rtrim($text, '0'), '.') : $text;
};
?>
<a href="<?= esc(url_to('detail-siswa', $siswa['id']), 'attr') ?>">Kembali ke Detail Siswa</a>
<div class="card card-body mt-3 mb-4">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <h2 class="h5 mb-0"><?= esc($siswa['nama']) ?></h2>
        <?= view('operator/siswa/status_arsip', ['statusArsip' => $status], ['saveData' => false]) ?>
    </div>
    <dl class="row g-3 mb-0">
        <?php foreach ([
            'Angkatan' => $siswa['tahun_masuk'],
            'Tahun pelajaran' => $rapor['tahun_pelajaran'] ?? null,
            'Kelas' => $rapor['kelas'] ?? null,
            'Rombel' => $rapor['rombel'] ?? null,
            'Kurikulum' => $rapor['kurikulum'] ?? null,
            'Format rapor' => $rapor['nama_format'] ?? null,
        ] as $label => $value): ?>
            <div class="col-6 col-lg-4">
                <dt><?= esc($label) ?></dt>
                <dd class="mb-0"><?= esc($value ?? '—') ?></dd>
            </div>
        <?php endforeach ?>
    </dl>
</div>

<?php if ($nilai !== []): ?>
    <?php
    $kolomNilai = [
        'sks' => 'SKS',
        'beban_jp' => 'Beban JP',
        'jp' => 'JP',
        'nilai_pengetahuan' => 'Pengetahuan',
        'nilai_keterampilan' => 'Keterampilan',
        'rata_rata_ard' => 'Rata-rata ARD',
        'nilai_akhir' => 'Nilai akhir',
    ];
    $kolomNilai = array_filter($kolomNilai, static fn ($field) => array_filter(array_column($nilai, $field), static fn ($value) => $value !== null) !== [], ARRAY_FILTER_USE_KEY);
    ?>
    <section class="card mb-4" aria-labelledby="judul-nilai">
        <div class="card-body"><h2 id="judul-nilai" class="h5 mb-0">Nilai Mata Pelajaran</h2></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Mata pelajaran</th>
                        <?php foreach ($kolomNilai as $label): ?>
                            <th scope="col" class="text-end text-nowrap"><?= esc($label) ?></th>
                        <?php endforeach ?>
                        <th scope="col">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilai as $item): ?>
                        <tr>
                            <th scope="row" class="fw-semibold text-none"><?= esc($item['nama_mapel_asli']) ?></th>
                            <?php foreach ($kolomNilai as $field => $label): ?>
                                <td class="text-end">
                                    <?= esc($angka($item[$field])) ?>
                                    <?php if ($field === 'nilai_pengetahuan' && $item['predikat_pengetahuan'] !== null): ?>
                                        <small class="d-block"><?= esc($item['predikat_pengetahuan']) ?></small>
                                    <?php elseif ($field === 'nilai_keterampilan' && $item['predikat_keterampilan'] !== null): ?>
                                        <small class="d-block"><?= esc($item['predikat_keterampilan']) ?></small>
                                    <?php endif ?>
                                </td>
                            <?php endforeach ?>
                            <td class="rmd-deskripsi">
                                <?php foreach ([
                                    'Pengetahuan' => $item['deskripsi_pengetahuan'],
                                    'Keterampilan' => $item['deskripsi_keterampilan'],
                                    'Capaian kompetensi' => $item['capaian_kompetensi'],
                                ] as $label => $teks): ?>
                                    <?php if ($teks !== null): ?>
                                        <p class="mb-2"><strong><?= esc($label) ?></strong> <?= esc($teks) ?></p>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif ?>

<?php if ($pendukung !== []): ?>
    <section class="card mb-4" aria-labelledby="judul-pendukung">
        <div class="card-body"><h2 id="judul-pendukung" class="h5 mb-0">Data Pendukung</h2></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Jenis</th><th scope="col">Item</th><th scope="col">Nilai</th>
                        <th scope="col">Predikat</th><th scope="col">Jumlah</th><th scope="col">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendukung as $item): ?>
                        <tr>
                            <th scope="row" class="fw-semibold text-none"><?= esc($item['jenis_item']) ?></th>
                            <td><?= esc($item['nama_item'] ?? '—') ?></td>
                            <td><?= esc($angka($item['nilai'])) ?></td>
                            <td><?= esc($item['predikat'] ?? '—') ?></td>
                            <td class="text-nowrap"><?= esc($angka($item['jumlah'])) ?> <?= esc($item['satuan'] ?? '') ?></td>
                            <td><?= esc($item['keterangan'] ?? $item['isian_tercetak_asli'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif ?>

<?php if ($nilai === [] && $pendukung === []): ?>
    <p>Belum ada nilai atau data pendukung untuk semester ini.</p>
<?php endif ?>
<?= $this->endSection() ?>
