<?php
/**
 * UNA SERIE nella pagina Workout (una riga con kg, rep, stato).
 *
 * Variabili:
 *   $esercizioId = id dell'esercizio
 *   $n           = numero della serie (1, 2, 3...)
 *   $ultima      = la stessa serie dell'ultimo allenamento (o null se non c'era)
 *   $pesoPrevisto = il peso scritto nella scheda (usato se non c'è un'ultima volta)
 *   $stati       = elenco stati per la tendina
 *   $nomiStati   = [id => nome] per scrivere lo stato dell'ultima volta
 *
 * I nomi dei campi sono tipo  serie[5][2][rep]  ->  in PHP arriva come array:
 *   $_POST['serie'][5][2]['rep']  = esercizio 5, serie 2, rep
 */
$campo = "serie[{$esercizioId}][{$n}]";
$vecchio = "serie.{$esercizioId}.{$n}"; // per old() con la "dot notation"
?>
<div class="serie" data-serie>
    <span class="serie__numero numero" data-numero><?= $n ?></span>

    <input class="input numero serie__kg" name="<?= $campo ?>[peso]" value="<?= old($vecchio . '.peso') ?>"
           inputmode="decimal" autocomplete="off" aria-label="Kg serie <?= $n ?>"
           placeholder="<?= $ultima ? kg($ultima['peso']) : ($pesoPrevisto !== null ? kg($pesoPrevisto) : 'kg') ?>">

    <input class="input numero serie__rep" name="<?= $campo ?>[rep]" value="<?= old($vecchio . '.rep') ?>"
           inputmode="numeric" autocomplete="off" aria-label="Ripetizioni serie <?= $n ?>"
           placeholder="<?= $ultima ? (int) $ultima['rep'] : 'rep' ?>">

    <button type="button" class="serie__fatta" data-fatta aria-pressed="false" aria-label="Segna serie <?= $n ?> come fatta">
        <?= icona('check', 18) ?>
    </button>

    <div class="serie__sotto">
        <?php if ($ultima): ?>
            <button type="button" class="serie__ultima" data-copia data-peso="<?= kg($ultima['peso']) ?>" data-rep="<?= (int) $ultima['rep'] ?>"
                    title="Tocca per copiare">
                <?= icona('ripeti', 13) ?>
                <span>ultima <b class="numero"><?= serie_testo($ultima['peso'], $ultima['rep']) ?></b><?php if ($ultima['stato_id']): ?> · <?= esc($nomiStati[$ultima['stato_id']] ?? '') ?><?php endif ?></span>
            </button>
        <?php else: ?>
            <span class="serie__ultima serie__ultima--vuota">nessun dato precedente</span>
        <?php endif ?>

        <select class="input serie__stato" name="<?= $campo ?>[stato_id]" aria-label="Stato serie <?= $n ?>">
            <option value="">Pulita</option>
            <?php foreach ($stati as $stato): ?>
                <option value="<?= $stato['id'] ?>" <?= old($vecchio . '.stato_id') == $stato['id'] ? 'selected' : '' ?> title="<?= esc($stato['descrizione']) ?>">
                    <?= esc($stato['nome']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
</div>
