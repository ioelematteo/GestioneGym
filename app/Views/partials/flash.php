<?php
/**
 * MESSAGGI "FLASH": arrivano dal controller con ->with('successo', '...')
 * e durano una sola pagina (poi spariscono da soli).
 */
?>
<?php if (session('successo')): ?>
    <div class="flash" role="status">
        <?= icona('check', 18) ?> <span><?= esc(session('successo')) ?></span>
        <button type="button" class="flash__chiudi" data-chiudi-flash aria-label="Chiudi"><?= icona('x', 16) ?></button>
    </div>
<?php endif ?>

<?php if (session('errore') || session('error')): ?>
    <!-- 'error' (in inglese) è quello che usa Shield, es. "non hai i permessi" -->
    <div class="flash flash--errore" role="alert">
        <?= icona('x', 18) ?> <span><?= esc(session('errore') ?? session('error')) ?></span>
    </div>
<?php elseif (isset(session('errori')['serie'])): ?>
    <!-- errore della pagina Workout: non c'è un campo sotto cui scriverlo, lo mostro qui -->
    <div class="flash flash--errore" role="alert">
        <?= icona('x', 18) ?> <span><?= esc(session('errori')['serie']) ?></span>
    </div>
<?php elseif (session('errori')): ?>
    <div class="flash flash--errore" role="alert">
        <?= icona('x', 18) ?> <span>Qualcosa non va: controlla i campi segnati in rosso.</span>
    </div>
<?php endif ?>

<?php if (session('bozza_da_cancellare')): ?>
    <!-- il JS legge questo e cancella la bozza dell'allenamento appena salvato -->
    <span hidden data-cancella-bozza="<?= (int) session('bozza_da_cancellare') ?>"></span>
<?php endif ?>
