<?php
/**
 * CARD NUMERICA (le 4 in alto in Home)
 *
 * Variabili che riceve:
 *   $icona, $label, $valore, $unita (opz.), $piede (html, opz.), $grafico (html, opz.)
 */
?>
<article class="card stat">
    <div class="stat__icona"><?= icona($icona, 19) ?></div>
    <div>
        <h2 class="stat__label"><?= esc($label) ?></h2>
        <p class="stat__valore"><?= esc($valore) ?><?php if (! empty($unita)): ?><span class="stat__unita"> <?= esc($unita) ?></span><?php endif ?></p>
        <div class="stat__piede"><?= $piede ?? '' ?></div>
    </div>
    <?php if (! empty($grafico)): ?>
        <div class="stat__grafico"><?= $grafico ?></div>
    <?php endif ?>
</article>
