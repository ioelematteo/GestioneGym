<?php

/**
 * GYM HELPER - funzioni per FORMATTARE numeri e date
 *
 * Un helper in CodeIgniter è un file di funzioni "sciolte" (non dentro una classe).
 * Questo è caricato in automatico ovunque (vedi app/Config/Autoload.php -> $helpers),
 * quindi nelle viste puoi scrivere direttamente: <?= kg(82.5) ?>
 *
 * "if (! function_exists(...))" serve solo a non dichiarare due volte la stessa funzione.
 */

use App\Libraries\Statistiche;

if (! function_exists('kg')) {
    /**
     * Peso senza zeri inutili: 80.00 -> "80" | 82.50 -> "82.5"
     */
    function kg(float|string|null $peso): string
    {
        $testo = number_format((float) $peso, 2, '.', '');

        return rtrim(rtrim($testo, '0'), '.');
    }
}

if (! function_exists('volume_breve')) {
    /**
     * Volume compatto: 28400 -> "28.4k" | 950 -> "950"
     */
    function volume_breve(float|int $volume): string
    {
        if ($volume >= 1000) {
            return kg(round($volume / 1000, 1)) . 'k';
        }

        return (string) round($volume);
    }
}

if (! function_exists('con_segno')) {
    /**
     * Numero con il segno davanti: 1.2 -> "+1.2" | -0.8 -> "−0.8" | 0 -> "±0"
     */
    function con_segno(float|int $numero, int $decimali = 1): string
    {
        $valore = kg(round(abs($numero), $decimali));

        if ($numero > 0) {
            return '+' . $valore;
        }

        return $numero < 0 ? '−' . $valore : '±' . $valore;
    }
}

if (! function_exists('durata')) {
    /**
     * Minuti leggibili: 72 -> "1h 12m" | 45 -> "45m" | null -> "—"
     */
    function durata(?int $minuti): string
    {
        if (! $minuti) {
            return '—';
        }

        if ($minuti < 60) {
            return $minuti . 'm';
        }

        return sprintf('%dh %02dm', intdiv($minuti, 60), $minuti % 60);
    }
}

if (! function_exists('data_breve')) {
    /**
     * "2026-10-20 18:30:00" -> "20 Ott 2026"
     */
    function data_breve(string $data): string
    {
        $timestamp = strtotime($data);

        return date('j', $timestamp) . ' ' . Statistiche::MESI[date('n', $timestamp) - 1] . ' ' . date('Y', $timestamp);
    }
}

if (! function_exists('data_corta')) {
    /**
     * "2026-10-20" -> "20 Ott"
     */
    function data_corta(string $data): string
    {
        $timestamp = strtotime($data);

        return date('j', $timestamp) . ' ' . Statistiche::MESI[date('n', $timestamp) - 1];
    }
}

if (! function_exists('quando')) {
    /**
     * Data "umana": "oggi", "ieri", "3 giorni fa", "2 settimane fa", altrimenti "20 Ott 2026"
     */
    function quando(?string $data): string
    {
        if ($data === null) {
            return 'mai';
        }

        $giorni = (int) floor((strtotime('today') - strtotime(substr($data, 0, 10))) / 86400);

        return match (true) {
            $giorni <= 0  => 'oggi',
            $giorni === 1 => 'ieri',
            $giorni < 7   => "{$giorni} giorni fa",
            $giorni < 14  => '1 settimana fa',
            $giorni < 60  => intdiv($giorni, 7) . ' settimane fa',
            default       => data_breve($data),
        };
    }
}

if (! function_exists('iniziale_workout')) {
    /**
     * La lettera da mettere nel "disco": "Push A" -> "A" | "Legs" -> "L" | "Giorno 2" -> "2"
     */
    function iniziale_workout(string $nome): string
    {
        $parole = preg_split('/\s+/', trim($nome));
        $ultima = end($parole);

        if (count($parole) > 1 && mb_strlen($ultima) <= 2) {
            return mb_strtoupper($ultima);
        }

        return mb_strtoupper(mb_substr(trim($nome), 0, 1));
    }
}

if (! function_exists('serie_testo')) {
    /**
     * Una serie in testo: "80 kg × 6"  |  a corpo libero: "× 12"
     */
    function serie_testo(float|string $peso, int|string $rep): string
    {
        if ((float) $peso == 0) {
            return '× ' . $rep;
        }

        return kg($peso) . ' kg × ' . $rep;
    }
}

if (! function_exists('nome_utente')) {
    /**
     * Il nome dell'utente loggato (username, o la parte prima della @ dell'email).
     */
    function nome_utente(): string
    {
        $user = auth()->user();

        return $user->username ?: strstr((string) $user->email, '@', true);
    }
}

if (! function_exists('is_admin')) {
    /**
     * L'utente loggato è admin?
     */
    function is_admin(): bool
    {
        return auth()->loggedIn() && auth()->user()->inGroup('admin');
    }
}
