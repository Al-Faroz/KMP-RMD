<?php
// XAMPP serves the root index.php, while spark serve uses public/index.php.
$assetPath = realpath(FCPATH) === realpath(ROOTPATH) ? 'public/assets/' : 'assets/';
?>
<!doctype html>
<html lang="id" data-bs-theme="light" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> · RMD</title>
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'sneat/core.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'sneat/boxicons.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'rmd/app.css'), 'attr') ?>">
    <script defer src="<?= esc(base_url($assetPath . 'sneat/bootstrap.bundle.min.js'), 'attr') ?>"></script>
    <script defer src="<?= esc(base_url($assetPath . 'rmd/operator.js'), 'attr') ?>"></script>
    <?php if (isset($pageScript)): ?>
        <script defer src="<?= esc(base_url($assetPath . $pageScript), 'attr') ?>"></script>
    <?php endif ?>
</head>
<body>
    <a class="visually-hidden-focusable rmd-skip-link" href="#konten-utama">Lewati ke konten utama</a>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?= $this->include('layouts/partials/sidebar') ?>
            <div class="layout-page">
                <?= $this->include('layouts/partials/navbar') ?>
                <div class="content-wrapper">
                    <main id="konten-utama" class="container-xxl container-p-y flex-grow-1" tabindex="-1">
                        <h1 class="h3 mb-4"><?= esc($pageTitle) ?></h1>
                        <?= $this->renderSection('content') ?>
                    </main>
                    <?= $this->include('layouts/partials/footer') ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
