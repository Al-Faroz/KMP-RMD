<?php
$menuItems = [
    'data-siswa' => ['label' => 'Data Siswa', 'icon' => 'bx-group'],
    'cetak-rombel' => ['label' => 'Cetak Rombel', 'icon' => 'bx-printer'],
    'daftar-operator' => ['label' => 'Operator', 'icon' => 'bx-group'],
    'kode-akses-cetak' => ['label' => 'Kode Akses Cetak', 'icon' => 'bx-lock-alt'],
];
?>
<aside id="menu-operator" class="offcanvas-xl offcanvas-start rmd-sidebar" tabindex="-1" aria-labelledby="judul-menu-operator">
    <div class="menu menu-vertical rmd-menu">
        <div class="app-brand rmd-brand">
            <span class="rmd-brand-icon" aria-hidden="true"><i class="bx bx-book-open"></i></span>
            <div class="flex-grow-1">
                <div class="fw-bold text-heading" id="judul-menu-operator">RMD</div>
                <small class="d-block">Raport Digital Madrasah</small>
            </div>
            <button type="button" class="btn btn-icon btn-text-secondary d-xl-none" data-bs-dismiss="offcanvas" data-bs-target="#menu-operator" aria-label="Tutup menu">
                <i class="bx bx-x" aria-hidden="true"></i>
            </button>
        </div>
        <nav aria-label="Navigasi operator">
            <ul class="menu-inner">
                <?php foreach ($menuItems as $route => $item): ?>
                    <?php $isActive = $activeMenu === $route; ?>
                    <li class="menu-item<?= $isActive ? ' active' : '' ?>">
                        <a class="menu-link" href="<?= esc(url_to($route), 'attr') ?>"<?= $isActive ? ' aria-current="page"' : '' ?>>
                            <i class="menu-icon bx <?= esc($item['icon'], 'attr') ?>" aria-hidden="true"></i>
                            <span><?= esc($item['label']) ?></span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </nav>
    </div>
</aside>
