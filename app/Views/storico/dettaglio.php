<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/allenamento.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenuto') ?>

<a href="<?= site_url('storico') ?>" class="torna"><?= icona('freccia-sx', 16) ?> Storico</a>

<div class="testata testata--allenamento">
    <div class="testata__titolo">
        <span class="disco disco--grande disco--<?= esc($allenamento['workout_colore']) ?>"><?= esc(iniziale_workout($allenamento['workout_nome'])) ?></span>
        <div>
            <h1 class="titolo-pagina"><?= esc($allenamento['workout_nome']) ?></h1>
            <p class="sottotitolo"><?= data_breve($allenamento['data_esecuzione']) ?> · <?= quando($allenamento['data_esecuzione']) ?></p>
        </div>
    </div>
    <a href="<?= site_url('workout/' . $allenamento['workout_id']) ?>" class="btn"><?= icona('ripeti', 17) ?> Rifallo</a>
</div>

<div class="riepilogo">
    <div class="riepilogo__voce"><span class="etichetta">Durata</span><span class="numero"><?= durata($allenamento['durata_minuti'] ? (int) $allenamento['durata_minuti'] : null) ?></span></div>
    <div class="riepilogo__voce"><span class="etichetta">Volume</span><span class="numero"><?= volume_breve($volume) ?> kg</span></div>
    <div class="riepilogo__voce"><span class="etichetta">Serie</span><span class="numero"><?= $n_serie ?></span></div>
    <div class="riepilogo__voce"><span class="etichetta">Esercizi</span><span class="numero"><?= count($esercizi) ?></span></div>
</div>

<?php if ($allenamento['note']): ?>
    <p class="nota-precedente"><?= icona('nota', 16) ?> <span><?= esc($allenamento['note']) ?></span></p>
<?php endif ?>

<div class="griglia">
    <?php foreach ($esercizi as $es): ?>
        <section class="card col-6 esercizio">
            <header class="esercizio__testa">
                <div>
                    <h2 class="titolo-card"><?= esc($es['nome']) ?></h2>
                    <?php if ($es['gruppo']): ?><p class="esercizio__target"><?= esc($es['gruppo']) ?></p><?php endif ?>
                </div>
                <?php if ($es['record']): ?>
                    <span class="chip chip--accento" title="La serie migliore di oggi batte tutte quelle precedenti">Record personale</span>
                <?php endif ?>
            </header>

            <table class="tabella tabella--serie">
                <thead><tr><th>#</th><th>Serie</th><th>Stato</th></tr></thead>
                <tbody>
                    <?php foreach ($es['serie'] as $serie): ?>
                        <?php $stato = $stati[$serie['stato_id']] ?? null; ?>
                        <tr>
                            <td class="numero testo-3"><?= (int) $serie['numero_serie'] ?></td>
                            <td class="numero"><?= serie_testo($serie['peso'], $serie['rep']) ?></td>
                            <td><?php if ($stato): ?><span class="stato stato--<?= esc($stato['colore']) ?>"><?= esc($stato['nome']) ?></span><?php else: ?><span class="testo-3">—</span><?php endif ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
    <?php endforeach ?>
</div>

<form method="post" action="<?= site_url('storico/' . $allenamento['id'] . '/elimina') ?>" class="zona-pericolo"
      data-conferma="Eliminare questo allenamento e tutte le sue serie? Non si può annullare.">
    <?= csrf_field() ?>
    <div>
        <strong>Elimina allenamento</strong>
        <p class="testo-2">Utile se l'hai salvato per sbaglio. Le serie vengono cancellate.</p>
    </div>
    <button class="btn btn--pericolo"><?= icona('cestino', 17) ?> Elimina</button>
</form>

<?= $this->endSection() ?>
