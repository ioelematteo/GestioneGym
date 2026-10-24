<?php
/**
 * UNA RIGA ESERCIZIO nel form "Crea workout".
 *
 * Tutti i campi hanno nomi tipo  esercizi[nome][]  (con le [] vuote):
 * PHP li riceve come array in ordine -> la riga 1 è l'indice 0, la riga 2 l'indice 1, ...
 *
 * Variabili: $riga (array), $recuperi
 */
?>
<div class="riga-esercizio" data-riga-esercizio>
    <span class="riga-esercizio__numero numero" data-numero-riga></span>

    <div class="riga-esercizio__campi">
        <div class="campo campo--nome">
            <label class="campo__label">Esercizio</label>
            <input name="esercizi[nome][]" class="input" list="catalogo-esercizi" value="<?= esc($riga['nome']) ?>"
                   placeholder="es. Panca piana" autocomplete="off" data-nome-esercizio>
        </div>

        <div class="campo campo--corto">
            <label class="campo__label">Serie</label>
            <input name="esercizi[serie][]" class="input numero" type="number" min="1" max="10" value="<?= (int) $riga['serie'] ?>">
        </div>

        <div class="campo campo--corto">
            <label class="campo__label">Rep</label>
            <input name="esercizi[rep_target][]" class="input numero" value="<?= esc($riga['rep_target'] ?? '') ?>" placeholder="8-10" maxlength="20">
        </div>

        <div class="campo campo--corto">
            <label class="campo__label">Peso (kg)</label>
            <input name="esercizi[peso][]" class="input numero" inputmode="decimal" value="<?= esc(isset($riga['peso']) && $riga['peso'] !== '' && $riga['peso'] !== null ? kg($riga['peso']) : '') ?>" placeholder="80">
        </div>

        <div class="campo campo--corto">
            <label class="campo__label">Recupero</label>
            <select name="esercizi[recupero][]" class="input numero">
                <?php foreach ($recuperi as $secondi): ?>
                    <option value="<?= $secondi ?>" <?= (int) $riga['recupero_secondi'] === $secondi ? 'selected' : '' ?>><?= gmdate('i:s', $secondi) ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="campo campo--note">
            <label class="campo__label">Note</label>
            <input name="esercizi[note][]" class="input" value="<?= esc($riga['note'] ?? '') ?>" placeholder="es. sedile su 4, presa stretta" maxlength="255">
        </div>
    </div>

    <div class="riga-esercizio__azioni">
        <button type="button" class="btn btn--icona btn--fantasma" data-sposta="su" aria-label="Sposta su"><?= icona('chevron-su', 18) ?></button>
        <button type="button" class="btn btn--icona btn--fantasma" data-sposta="giu" aria-label="Sposta giù"><?= icona('chevron-giu', 18) ?></button>
        <button type="button" class="btn btn--icona btn--fantasma" data-rimuovi aria-label="Rimuovi esercizio"><?= icona('x', 18) ?></button>
    </div>
</div>
