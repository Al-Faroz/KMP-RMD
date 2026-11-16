<?php $pager->setSurroundCount(1); ?>
<nav aria-label="Halaman daftar siswa">
    <ul class="pagination flex-wrap mb-0">
        <?php if ($pager->hasPreviousPage()): ?>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getPreviousPage(), 'attr') ?>">Sebelumnya</a></li>
        <?php endif ?>
        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item<?= $link['active'] ? ' active' : '' ?>">
                <a class="page-link" href="<?= esc($link['uri'], 'attr') ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>><?= esc($link['title']) ?></a>
            </li>
        <?php endforeach ?>
        <?php if ($pager->hasNextPage()): ?>
            <li class="page-item"><a class="page-link" href="<?= esc($pager->getNextPage(), 'attr') ?>">Berikutnya</a></li>
        <?php endif ?>
    </ul>
</nav>
