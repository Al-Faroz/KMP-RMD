<?php
// XAMPP serves the root index.php, while spark serve uses public/index.php.
$assetPath = realpath(FCPATH) === realpath(ROOTPATH) ? 'public/assets/' : 'assets/';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Rapor Digital Madrasah</title>
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'sneat/core.css'), 'attr') ?>">
    <link rel="stylesheet" href="<?= esc(base_url($assetPath . 'rmd/masuk.css'), 'attr') ?>">
</head>
<body class="rmd-login">
    <main class="rmd-login-panel">
        <div class="rmd-login-brand">
            <img src="<?= esc(base_url($assetPath . 'rmd/logo-kemenag.jpg'), 'attr') ?>" width="42" height="38" alt="">
            <span>Rapor Digital Madrasah</span>
        </div>
        <h1 class="h3 mb-4">Masuk</h1>

        <?php if ($sukses = session()->getFlashdata('sukses')): ?>
            <p class="alert alert-success mb-3" role="status"><?= esc($sukses) ?></p>
        <?php endif ?>

        <?php if ($galat !== '' || ($galat = session()->getFlashdata('error'))): ?>
            <p class="alert alert-danger mb-3" role="alert"><?= esc($galat) ?></p>
        <?php endif ?>

        <form method="post" action="<?= esc(url_to('masuk'), 'attr') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" autocomplete="username"
                       value="<?= esc($username, 'attr') ?>" maxlength="64" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Kata sandi</label>
                <input type="password" id="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Masuk</button>
        </form>
        <a class="btn btn-outline-secondary w-100 mt-3"
           href="<?= esc(url_to('beranda-publik'), 'attr') ?>">Kembali ke Beranda</a>
    </main>
</body>
</html>
