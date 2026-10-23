<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/crea-workout.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenuto') ?>
<?php
$inModifica = $workout !== null;
$azione     = $inModifica ? 'workouts/' . $workout['id'] : 'workouts';
$coloreScelto = old('colore', $workout['colore'] ?? 'verde');
?>

<?php if ($inModifica): ?>
    <a href="<?= site_url('workout') ?>" class="torna"><?= icona('freccia-sx', 16) ?> Tutti i workout</a>
<?php endif ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina"><?= $inModifica ? 'Modifica workout' : 'Crea workout' ?></h1>
        <p class="sottotitolo">Dai un nome alla scheda e mettici dentro gli esercizi, nell'ordine in cui li fai.</p>
    </div>
</div>

<form method="post" action="<?= site_url($azione) ?>" class="crea-workout" data-crea-workout>
    <?= csrf_field() ?>

    <div class="griglia">
        <!-- ====== DETTAGLI ====== -->
        <section class="card col-4 dettagli">
            <h2 class="titolo-card">Dettagli</h2>

            <div class="campo">
                <label class="campo__label" for="nome">Nome</label>
                <input id="nome" name="nome" class="input" value="<?= old('nome', $workout['nome'] ?? '') ?>" placeholder="es. Push A" maxlength="100" required>
                <?= errore('nome') ?>
            </div>

            <div class="campo">
                <label class="campo__label" for="descrizione">Descrizione <span class="testo-3">(facoltativa)</span></label>
                <input id="descrizione" name="descrizione" class="input" value="<?= old('descrizione', $workout['descrizione'] ?? '') ?>" placeholder="es. Petto, spalle e tricipiti" maxlength="255">
                <?= errore('descrizione') ?>
            </div>

            <fieldset class="campo colori">
                <legend class="campo__label">Colore</legend>
                <div class="colori__lista">
                    <?php foreach ($colori as $chiave => $nomeColore): ?>
                        <label class="colori__opzione" title="<?= esc($nomeColore) ?>">
                            <input type="radio" name="colore" value="<?= $chiave ?>" <?= $coloreScelto === $chiave ? 'checked' : '' ?>>
                            <span class="disco disco--<?= $chiave ?>" data-anteprima-iniziale><?= esc(iniziale_workout($workout['nome'] ?? 'A')) ?></span>
                            <span class="solo-lettori"><?= esc($nomeColore) ?></span>
                        </label>
                    <?php endforeach ?>
                </div>
                <?= errore('colore') ?>
            </fieldset>
        </section>

        <!-- ====== ESERCIZI ====== -->
        <section class="card col-8">
            <div class="card__testa">
                <div>
                    <h2 class="titolo-card">Esercizi</h2>
                    <p class="card__sotto">Scrivi il nome: se esiste già te lo suggerisco, se è nuovo lo creo io.</p>
                </div>
            </div>

            <?= errore('esercizi') ?>

            <div class="righe-esercizi" data-lista-esercizi>
                <?php foreach ($righe as $riga): ?>
                    <?= componente('workouts/_riga_esercizio', ['riga' => $riga, 'gruppi' => $gruppi, 'recuperi' => $recuperi]) ?>
                <?php endforeach ?>
            </div>

            <button type="button" class="btn aggiungi-esercizio" data-aggiungi-esercizio><?= icona('piu', 18) ?> Aggiungi esercizio</button>
        </section>
    </div>

    <div class="form-azioni form-azioni--fondo">
        <button class="btn btn--primario btn--grande"><?= icona('check', 18) ?> <?= $inModifica ? 'Salva modifiche' : 'Crea workout' ?></button>
        <a href="<?= site_url('workout') ?>" class="btn btn--fantasma btn--grande">Annulla</a>
    </div>
</form>

<?php if ($inModifica): ?>
    <!-- form separato: i form HTML non si possono mettere uno dentro l'altro -->
    <form method="post" action="<?= site_url('workouts/' . $workout['id'] . '/elimina') ?>" class="zona-pericolo"
          data-conferma="Eliminare «<?= esc($workout['nome']) ?>»? Gli allenamenti già fatti restano nello storico.">
        <?= csrf_field() ?>
        <div>
            <strong>Elimina workout</strong>
            <p class="testo-2">Sparisce dalla lista, ma lo storico degli allenamenti resta.</p>
        </div>
        <button class="btn btn--pericolo"><?= icona('cestino', 17) ?> Elimina</button>
    </form>
<?php endif ?>

<!-- Suggerimenti per il campo "Esercizio" (+ gruppo muscolare per riempirlo da solo) -->
<datalist id="catalogo-esercizi">
    <?php foreach ($catalogo as $esercizio): ?>
        <option value="<?= esc($esercizio['nome']) ?>" data-gruppo="<?= esc($esercizio['gruppo_muscolare'] ?? '') ?>"></option>
    <?php endforeach ?>
</datalist>

<!-- Modello di una riga vuota: il JS lo copia quando premi "Aggiungi esercizio" -->
<template id="modello-esercizio">
    <?= componente('workouts/_riga_esercizio', [
        'riga'     => ['nome' => '', 'gruppo_muscolare' => '', 'serie' => 3, 'rep_target' => '8-10', 'recupero_secondi' => 90, 'note' => ''],
        'gruppi'   => $gruppi,
        'recuperi' => $recuperi,
    ]) ?>
</template>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/crea-workout.js') ?>" defer></script>
<?= $this->endSection() ?>
