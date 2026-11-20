<?php
// Use the asset prefix for the active front controller.
$assetPath = realpath(FCPATH) === realpath(ROOTPATH) ? 'public/assets/' : 'assets/';
?>
<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($pageTitle) ?> · Rapor Digital Madrasah</title>
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'sneat/core.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'rmd/app.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'rmd/publik.css'), 'attr') ?>">
    <?php foreach ($pageScripts as $script): ?>
        <script defer src="<?= esc(base_url($assetPath . $script), 'attr') ?>"></script>
    <?php endforeach ?>
</head>
<body class="rmd-public">
    <a class="visually-hidden-focusable rmd-skip-link" href="#konten-utama">Lewati ke konten utama</a>
    <header class="rmd-public-header">
        <div class="container rmd-public-wrap d-flex align-items-center justify-content-between gap-3 py-3">
            <a class="rmd-public-brand" href="<?= esc(url_to('beranda-publik'), 'attr') ?>">
                <img src="<?= esc(base_url($assetPath . 'rmd/logo-kemenag.jpg'), 'attr') ?>" width="42" height="38" alt="">
                <span><strong>Rapor Digital Madrasah</strong><small>MTsN 4 Jombang</small></span>
            </a>
            <a class="btn btn-outline-primary btn-sm text-nowrap" href="<?= esc(url_to('masuk'), 'attr') ?>">Masuk Operator</a>
        </div>
    </header>
    <main id="konten-utama" tabindex="-1">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
