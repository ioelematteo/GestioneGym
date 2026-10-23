<?php
/**
 * PAGINAZIONE SEMPLICE: "← Più recenti   Pagina 2 di 5   Più vecchi →"
 * CodeIgniter passa qui l'oggetto $pager (vedi app/Config/Pager.php).
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
?>
<?php if ($pager->getPageCount() > 1): ?>
    <nav class="pager" aria-label="Pagine">
        <?php if ($pager->hasPreviousPage()): ?>
            <a href="<?= $pager->getPreviousPage() ?>" class="btn btn--piccolo"><?= icona('freccia-sx', 16) ?> Più recenti</a>
        <?php else: ?>
            <span></span>
        <?php endif ?>

        <span class="pager__info">Pagina <?= $pager->getCurrentPageNumber() ?> di <?= $pager->getPageCount() ?></span>

        <?php if ($pager->hasNextPage()): ?>
            <a href="<?= $pager->getNextPage() ?>" class="btn btn--piccolo">Più vecchi <?= icona('freccia-dx', 16) ?></a>
        <?php else: ?>
            <span></span>
        <?php endif ?>
    </nav>
<?php endif ?>
