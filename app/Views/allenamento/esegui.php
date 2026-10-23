<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/allenamento.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenuto') ?>
<?php
// [id => nome] degli stati, per scrivere "ultima 80 × 6 · Spottato"
$nomiStati = array_column($stati, 'nome', 'id');
?>

<a href="<?= site_url('workout') ?>" class="torna"><?= icona('freccia-sx', 16) ?> Tutti i workout</a>

<div class="testata testata--allenamento">
    <div class="testata__titolo">
        <span class="disco disco--grande disco--<?= esc($workout['colore']) ?>"><?= esc(iniziale_workout($workout['nome'])) ?></span>
        <div>
            <h1 class="titolo-pagina"><?= esc($workout['nome']) ?></h1>
            <p class="sottotitolo">
                <?php if ($ultimo): ?>
                    Ultima volta <?= quando($ultimo['data_esecuzione']) ?> · <?= durata($ultimo['durata_minuti'] ? (int) $ultimo['durata_minuti'] : null) ?>
                <?php else: ?>
                    Prima volta: niente numeri precedenti, oggi fissi il punto di partenza.
                <?php endif ?>
            </p>
        </div>
    </div>

    <div class="cronometro" title="Da quanto ti stai allenando">
        <?= icona('timer', 18) ?> <span class="numero" data-cronometro>00:00</span>
    </div>
</div>

<div class="flash bozza" data-avviso-bozza hidden>
    <?= icona('ripeti', 18) ?>
    <span>Ho ripreso l'allenamento che avevi lasciato a metà.</span>
    <button type="button" class="btn btn--piccolo btn--fantasma" data-scarta-bozza>Ricomincia da zero</button>
</div>

<?php if ($ultimo && $ultimo['note']): ?>
    <p class="nota-precedente"><?= icona('nota', 16) ?> <span><b>Nota dell'ultima volta:</b> <?= esc($ultimo['note']) ?></span></p>
<?php endif ?>

<form method="post" action="<?= site_url('workout/' . $workout['id']) ?>" class="allenamento" data-allenamento="<?= $workout['id'] ?>">
    <?= csrf_field() ?>

    <?php foreach ($esercizi as $es): ?>
        <?php
        $ultimeSerie = $ultime[$es['esercizio_id']] ?? [];
        // mostro tante righe quante le serie previste, o di più se l'ultima volta ne hai fatte di più
        $righe = max((int) $es['serie'], count($ultimeSerie) ? max(array_keys($ultimeSerie)) : 0);
        ?>
        <section class="card esercizio" data-esercizio="<?= $es['esercizio_id'] ?>" data-recupero="<?= (int) $es['recupero_secondi'] ?>">
            <header class="esercizio__testa">
                <div>
                    <h2 class="titolo-card"><?= esc($es['nome']) ?></h2>
                    <p class="esercizio__target">
                        <span class="numero"><?= (int) $es['serie'] ?> × <?= esc($es['rep_target'] ?: '—') ?></span>
                        · recupero <span class="numero"><?= gmdate('i:s', (int) $es['recupero_secondi']) ?></span>
                    </p>
                </div>
                <?php if ($es['gruppo_muscolare']): ?>
                    <span class="chip"><?= esc($es['gruppo_muscolare']) ?></span>
                <?php endif ?>
            </header>

            <?php if ($es['note']): ?>
                <p class="esercizio__nota"><?= icona('nota', 15) ?> <?= esc($es['note']) ?></p>
            <?php endif ?>

            <div class="serie-intestazione" aria-hidden="true">
                <span>#</span><span>Kg</span><span>Rep</span><span></span>
            </div>

            <div data-serie-lista>
                <?php for ($n = 1; $n <= $righe; $n++): ?>
                    <?= componente('allenamento/_serie', [
                        'esercizioId' => $es['esercizio_id'],
                        'n'           => $n,
                        'ultima'      => $ultimeSerie[$n] ?? null,
                        'stati'       => $stati,
                        'nomiStati'   => $nomiStati,
                    ]) ?>
                <?php endfor ?>
            </div>

            <button type="button" class="btn btn--fantasma btn--piccolo aggiungi-serie" data-aggiungi-serie>
                <?= icona('piu', 16) ?> Aggiungi serie
            </button>
        </section>
    <?php endforeach ?>

    <section class="card chiusura">
        <h2 class="titolo-card">Chiudi allenamento</h2>
        <p class="card__sotto">Si salvano solo le serie dove hai scritto le rep.</p>

        <div class="form-riga chiusura__campi">
            <div class="campo">
                <label class="campo__label" for="data">Data</label>
                <input type="date" id="data" name="data" class="input" value="<?= old('data', date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>">
                <?= errore('data') ?>
            </div>
            <div class="campo">
                <label class="campo__label" for="peso_corporeo">Peso corporeo <span class="testo-3">(facoltativo)</span></label>
                <input id="peso_corporeo" name="peso_corporeo" class="input numero" inputmode="decimal" placeholder="es. 56.8" value="<?= old('peso_corporeo') ?>">
                <?= errore('peso_corporeo') ?>
            </div>
        </div>

        <div class="campo">
            <label class="campo__label" for="note">Note</label>
            <textarea id="note" name="note" class="input" rows="2" placeholder="Come è andata? Sonno, energia, fastidi..."><?= old('note') ?></textarea>
            <?= errore('note') ?>
        </div>

        <!-- la durata la riempie il cronometro (JavaScript) quando premi Salva -->
        <input type="hidden" name="durata_minuti" value="<?= old('durata_minuti') ?>" data-durata>

        <button class="btn btn--primario btn--grande btn--blocco"><?= icona('check', 18) ?> Salva allenamento</button>
    </section>
</form>

<!-- Timer di recupero: compare quando segni una serie come fatta -->
<div class="recupero" data-recupero-box hidden role="timer" aria-live="polite">
    <div class="recupero__barra"><span data-recupero-progresso></span></div>
    <span class="recupero__label">Recupero</span>
    <span class="recupero__tempo numero" data-recupero-tempo>1:30</span>
    <button type="button" class="btn btn--piccolo" data-recupero-piu>+15s</button>
    <button type="button" class="btn btn--piccolo btn--fantasma" data-recupero-stop>Salta</button>
</div>

<!-- Modello per "Aggiungi serie": il JS lo copia e sostituisce __ES__ e __N__ -->
<template id="modello-serie">
    <?= componente('allenamento/_serie', [
        'esercizioId' => '__ES__',
        'n'           => '__N__',
        'ultima'      => null,
        'stati'       => $stati,
        'nomiStati'   => $nomiStati,
    ]) ?>
</template>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/js/allenamento.js') ?>" defer></script>
<?= $this->endSection() ?>
