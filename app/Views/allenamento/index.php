<?= $this->extend('layouts/app') ?>

<?= $this->section('contenuto') ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina">Workout</h1>
        <p class="sottotitolo">Cosa alleni oggi? Scegli e inizia a segnare le serie.</p>
    </div>
    <a href="<?= site_url('workouts/nuovo') ?>" class="btn"><?= icona('piu', 18) ?> Crea workout</a>
</div>

<?php if ($workouts === []): ?>
    <div class="card">
        <?= componente('partials/vuoto', [
            'icona'   => 'bilanciere',
            'titolo'  => 'Non hai ancora nessun workout',
            'testo'   => 'Un workout è la tua scheda: esercizi, serie, rep e recupero.',
            'link'    => 'workouts/nuovo',
            'bottone' => 'Crea il primo',
        ]) ?>
    </div>
<?php else: ?>
    <div class="griglia-workout">
        <?php foreach ($workouts as $i => $w): ?>
            <?= componente('partials/card_workout', ['w' => $w, 'consigliato' => $i === 0 && count($workouts) > 1, 'modifica' => true]) ?>
        <?php endforeach ?>
    </div>
<?php endif ?>

<?= $this->endSection() ?>
