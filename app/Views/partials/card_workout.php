<?php
/**
 * CARD DI UN WORKOUT (Home -> "Prossimi workout" e pagina Workout)
 *
 * Variabili:
 *   $w           = workout con riepilogo (vedi WorkoutModel::conRiepilogo)
 *   $consigliato = true se è quello che "tocca adesso"
 *   $modifica    = true per mostrare la matita (opzionale)
 */
?>
<div class="card-workout<?= ! empty($consigliato) ? ' card-workout--consigliato' : '' ?>">
    <a href="<?= site_url('workout/' . $w['id']) ?>" class="card-workout__link">
        <span class="disco disco--<?= esc($w['colore']) ?>"><?= esc(iniziale_workout($w['nome'])) ?></span>

        <span class="card-workout__testo">
            <span class="card-workout__nome">
                <?= esc($w['nome']) ?>
                <?php if (! empty($consigliato)): ?><span class="chip chip--accento">Tocca a questo</span><?php endif ?>
            </span>
            <?php if ($w['gruppi'] !== []): ?>
                <span class="card-workout__gruppi"><?= esc(implode(' · ', $w['gruppi'])) ?></span>
            <?php endif ?>
            <span class="card-workout__info">
                <?= $w['n_esercizi'] ?> esercizi · ~<?= $w['durata_stimata'] ?> min · <?= $w['ultima_volta'] ? 'fatto ' . quando($w['ultima_volta']) : 'mai fatto' ?>
            </span>
        </span>

        <span class="card-workout__play" aria-label="Inizia"><?= icona('play', 16) ?></span>
    </a>

    <?php if (! empty($modifica)): ?>
        <a href="<?= site_url('workouts/' . $w['id'] . '/modifica') ?>" class="btn btn--icona btn--fantasma card-workout__modifica" aria-label="Modifica <?= esc($w['nome']) ?>">
            <?= icona('matita', 17) ?>
        </a>
    <?php endif ?>
</div>
