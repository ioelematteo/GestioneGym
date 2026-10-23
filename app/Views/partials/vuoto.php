<?php
/**
 * STATO VUOTO: quando non c'è ancora niente da mostrare.
 * Variabili: $icona, $titolo, $testo, $link (opz.), $bottone (opz.)
 */
?>
<div class="vuoto">
    <span class="vuoto__icona"><?= icona($icona) ?></span>
    <strong><?= esc($titolo) ?></strong>
    <span><?= esc($testo) ?></span>
    <?php if (! empty($link)): ?>
        <a href="<?= site_url($link) ?>" class="btn btn--piccolo"><?= esc($bottone) ?></a>
    <?php endif ?>
</div>
