<header class="layout-navbar navbar navbar-detached container-xxl bg-white gap-3 justify-content-start">
    <button id="buka-menu-operator" type="button" class="btn btn-icon btn-text-secondary d-xl-none" data-bs-toggle="offcanvas" data-bs-target="#menu-operator" aria-controls="menu-operator" aria-expanded="false" aria-label="Buka menu">
        <i class="bx bx-menu" aria-hidden="true"></i>
    </button>
    <button id="toggle-menu-desktop" type="button" class="btn btn-icon btn-text-secondary d-none d-xl-inline-flex" aria-controls="menu-operator" aria-expanded="true" aria-label="Tutup menu">
        <i class="bx bx-menu" aria-hidden="true"></i>
    </button>
    <span class="fw-semibold text-heading">Ruang Operator</span>
    <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
        <span class="small text-break"><?= esc(session()->get('operator_nama')) ?></span>
        <form method="post" action="<?= esc(url_to('keluar'), 'attr') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-secondary">Keluar</button>
        </form>
    </div>
</header>
