<?php

/**
 * UI HELPER - funzioni che restituiscono pezzetti di HTML/SVG
 *
 * Esempi nelle viste:
 *   <?= icona('home') ?>
 *   <?= trend(+3) ?>
 *   <?= sparkline([54.1, 54.6, 55.2]) ?>
 *   <?= errore('nome') ?>
 */

if (! function_exists('componente')) {
    /**
     * Mostra un pezzo di vista riutilizzabile (es. una card, una riga, uno stato vuoto).
     *
     *   <?= componente('partials/vuoto', ['icona' => 'storico', 'titolo' => '...']) ?>
     *
     * Perché non usare direttamente view()? Perché view() "ricorda" i dati tra una
     * chiamata e l'altra: un 'link' passato al primo pezzo comparirebbe anche nel secondo.
     * Con saveData = false ogni pezzo vede SOLO i dati che gli passi.
     */
    function componente(string $vista, array $dati = []): string
    {
        return view($vista, $dati, ['saveData' => false]);
    }
}

if (! function_exists('icona')) {
    /**
     * Un'icona SVG (disegnate "a linea", prendono il colore del testo intorno).
     */
    function icona(string $nome, int $dimensione = 20): string
    {
        $icone = [
            'home'        => '<path d="M15 21v-7a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v7"/><path d="M3 10a2 2 0 0 1 .7-1.5l7-6a2 2 0 0 1 2.6 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
            'bilanciere'  => '<path d="M6.5 6v12M3.5 9v6M17.5 6v12M20.5 9v6M6.5 12h11"/>',
            'piu'         => '<path d="M12 5v14M5 12h14"/>',
            'storico'     => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>',
            'utenti'      => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
            'utente'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'freccia-dx'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'freccia-sx'  => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'su'          => '<path d="M12 19V5M6 11l6-6 6 6"/>',
            'giu'         => '<path d="M12 5v14M6 13l6 6 6-6"/>',
            'chevron-giu' => '<path d="m6 9 6 6 6-6"/>',
            'chevron-su'  => '<path d="m6 15 6-6 6 6"/>',
            'chevron-dx'  => '<path d="m9 6 6 6-6 6"/>',
            'play'        => '<path d="M8 5.5v13l11-6.5z" fill="currentColor"/>',
            'check'       => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
            'x'           => '<path d="M6 6l12 12M18 6 6 18"/>',
            'cestino'     => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
            'matita'      => '<path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16z"/><path d="m13.5 6.5 4 4"/>',
            'esci'        => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
            'bilancia'    => '<rect x="3" y="3" width="18" height="18" rx="4"/><path d="M8 9.5a5.5 5.5 0 0 1 8 0l-2.6 2.8"/>',
            'strati'      => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
            'barre'       => '<path d="M5 20v-8M12 20V5M19 20v-11"/>',
            'calendario'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
            'timer'       => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2M9 2h6"/>',
            'lucchetto'   => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
            'sblocca'     => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 7.5-2"/>',
            'ripeti'      => '<path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/>',
            'nota'        => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
        ];

        $disegno = $icone[$nome] ?? '';

        return '<svg class="icona" width="' . $dimensione . '" height="' . $dimensione . '" viewBox="0 0 24 24" fill="none" '
            . 'stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            . $disegno . '</svg>';
    }
}

if (! function_exists('trend')) {
    /**
     * La "pillola" con freccia e variazione: ↑ +3 | ↓ −1.2 kg
     *
     * $neutro = true per i valori dove "su" non è per forza meglio (es. peso corporeo).
     */
    function trend(float|int|null $delta, string $unita = '', bool $neutro = false): string
    {
        if ($delta === null) {
            return '';
        }

        $classe = match (true) {
            $neutro || $delta == 0 => 'trend--neutro',
            $delta > 0             => 'trend--su',
            default                => 'trend--giu',
        };

        $freccia = $delta == 0 ? '' : icona($delta > 0 ? 'su' : 'giu', 13);

        return '<span class="trend ' . $classe . '">' . $freccia . esc(con_segno($delta) . $unita) . '</span>';
    }
}

if (! function_exists('sparkline')) {
    /**
     * Mini grafico a linea in SVG (per la card del peso).
     * Calcolo i punti: x va da sinistra a destra, y dal basso (min) all'alto (max).
     */
    function sparkline(array $valori, int $larghezza = 112, int $altezza = 36): string
    {
        if (count($valori) < 2) {
            return '';
        }

        $min   = min($valori);
        $max   = max($valori);
        $range = ($max - $min) ?: 1;
        $passo = $larghezza / (count($valori) - 1);
        $punti = [];

        foreach (array_values($valori) as $i => $valore) {
            $x       = round($i * $passo, 1);
            $y       = round($altezza - 4 - (($valore - $min) / $range) * ($altezza - 8), 1);
            $punti[] = "{$x},{$y}";
        }

        $linea = 'M' . implode(' L', $punti);
        $area  = $linea . " L{$larghezza},{$altezza} L0,{$altezza} Z";
        [$ultimoX, $ultimoY] = explode(',', end($punti));

        return '<svg class="sparkline" viewBox="-4 0 ' . ($larghezza + 8) . ' ' . $altezza . '" width="' . $larghezza . '" height="' . $altezza . '" aria-hidden="true">'
            . '<path class="sparkline__area" d="' . $area . '"/>'
            . '<path class="sparkline__linea" d="' . $linea . '"/>'
            . '<circle class="sparkline__punto" cx="' . $ultimoX . '" cy="' . $ultimoY . '" r="3.5"/>'
            . '</svg>';
    }
}

if (! function_exists('mini_barre')) {
    /**
     * Mini grafico a barre in SVG (per la card del volume). L'ultima barra è evidenziata.
     */
    function mini_barre(array $valori, int $larghezza = 112, int $altezza = 36): string
    {
        $valori = array_slice(array_values($valori), -16); // massimo 16 barre

        if ($valori === []) {
            return '';
        }

        $max    = max($valori) ?: 1;
        $spazio = $larghezza / count($valori);
        $barra  = min(6, $spazio - 2);
        $svg    = '';

        foreach ($valori as $i => $valore) {
            $h      = max(2, round(($valore / $max) * $altezza, 1));
            $x      = round($i * $spazio + ($spazio - $barra) / 2, 1);
            $classe = $i === count($valori) - 1 ? 'mini-barre__barra mini-barre__barra--ultima' : 'mini-barre__barra';
            $svg   .= '<rect class="' . $classe . '" x="' . $x . '" y="' . ($altezza - $h) . '" width="' . $barra . '" height="' . $h . '" rx="1.5"/>';
        }

        return '<svg class="mini-barre" viewBox="0 0 ' . $larghezza . ' ' . $altezza . '" width="' . $larghezza . '" height="' . $altezza . '" aria-hidden="true">' . $svg . '</svg>';
    }
}

if (! function_exists('errore')) {
    /**
     * Mostra l'errore di validazione di un campo (se c'è).
     * Gli errori arrivano dal controller con ->with('errori', [...])
     */
    function errore(string $campo): string
    {
        $errori = session('errori') ?? [];

        if (! isset($errori[$campo])) {
            return '';
        }

        return '<p class="campo__errore">' . esc($errori[$campo]) . '</p>';
    }
}
