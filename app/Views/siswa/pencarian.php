<div class="col-md position-relative" id="pencarian-siswa"
     data-url-saran="<?= esc($urlSaran, 'attr') ?>">
    <label for="kata-siswa" class="form-label">Nama, NIS, atau NISN</label>
    <input id="kata-siswa" name="q" type="search" class="form-control"
           value="<?= esc($kata, 'attr') ?>" maxlength="100" autocomplete="off">
    <div id="saran-siswa" class="rmd-saran-siswa card mt-1" hidden>
        <ul id="opsi-siswa" class="list-group list-group-flush" role="listbox"
            aria-label="Saran siswa" hidden></ul>
        <p id="pesan-saran-siswa" class="px-3 py-2 mb-0" hidden></p>
    </div>
    <p id="status-saran-siswa" class="visually-hidden" role="status"></p>
</div>
<div class="col-md-3">
    <label for="angkatan-siswa" class="form-label">Angkatan</label>
    <select id="angkatan-siswa" name="angkatan" class="form-select">
        <option value="">Semua angkatan</option>
        <?php foreach ($daftarAngkatan as $item): ?>
            <?php $tahun = (string) $item['tahun_masuk']; ?>
            <option value="<?= esc($tahun, 'attr') ?>" <?= $angkatan === $tahun ? 'selected' : '' ?>><?= esc($tahun) ?></option>
        <?php endforeach ?>
    </select>
</div>
