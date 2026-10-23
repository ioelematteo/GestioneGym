<?php
/**
 * UNA RIGA "ALLENAMENTO FATTO" (Home e Storico)
 * Variabile: $a = riga di singolo_workout con workout_nome, workout_colore, volume, n_serie
 */
?>
<a href="<?= site_url('storico/' . $a['id']) ?>" class="riga">
    <span class="disco disco--<?= esc($a['workout_colore']) ?>"><?= esc(iniziale_workout($a['workout_nome'])) ?></span>
    <span class="riga__testo">
        <span class="riga__titolo"><?= esc($a['workout_nome']) ?></span>
        <span class="riga__sotto"><?= data_breve($a['data_esecuzione']) ?> · <?= durata($a['durata_minuti'] ? (int) $a['durata_minuti'] : null) ?></span>
    </span>
    <span class="riga__destra">
        <span class="etichetta">Volume</span><br>
        <span class="riga__valore"><?= volume_breve($a['volume']) ?> kg</span>
    </span>
</a>
