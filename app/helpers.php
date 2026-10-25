<?php

/**
 * FUNZIONI PER FORMATTARE nelle viste: {{ kg(82.5) }}
 * Caricate da composer.json -> autoload.files (sono disponibili ovunque).
 */

if (! function_exists('kg')) {
    /** 80.0 -> "80" | 82.5 -> "82.5" */
    function kg(float|int|string|null $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}

if (! function_exists('volume_breve')) {
    /** 28400 -> "28.4k" */
    function volume_breve(float|int $value): string
    {
        return $value >= 1000 ? kg(round($value / 1000, 1)).'k' : (string) round($value);
    }
}

if (! function_exists('durata')) {
    /** 72 -> "1h 12m" | 45 -> "45m" | null -> "—" */
    function durata(?int $minutes): string
    {
        if (! $minutes) {
            return '—';
        }

        return $minutes < 60 ? "{$minutes}m" : sprintf('%dh %02dm', intdiv($minutes, 60), $minutes % 60);
    }
}

if (! function_exists('serie')) {
    /** "16 × 9" oppure, a corpo libero, "× 9" */
    function serie(float|string $weight, int|string $reps): string
    {
        return ((float) $weight > 0 ? kg($weight).' kg ' : '').'× '.$reps;
    }
}
