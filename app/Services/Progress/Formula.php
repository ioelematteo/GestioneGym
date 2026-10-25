<?php

namespace App\Services\Progress;

/**
 * FORMULE PURE (niente database): numeri dentro, numeri fuori.
 * Sono statiche: Formula::change(75, 80) -> 6.7
 */
final class Formula
{
    /** Variazione % da $before a $after. Da zero non si calcola: null. */
    public static function change(float $before, float $after): ?float
    {
        return $before == 0.0 ? null : round(($after - $before) / $before * 100, 1);
    }

    /** Massimale stimato (Epley): peso × (1 + rep / 30). */
    public static function oneRepMax(float $weight, int $reps): float
    {
        return $reps <= 1 ? $weight : round($weight * (1 + $reps / 30), 1);
    }

    /** Punteggio di una serie: col peso il massimale stimato, a corpo libero le rep. */
    public static function setScore(float $weight, int $reps): float
    {
        return $weight > 0 ? self::oneRepMax($weight, $reps) : (float) $reps;
    }

    /** Media semplice, 0 se la lista è vuota. */
    public static function average(array $values): float
    {
        return $values === [] ? 0.0 : array_sum($values) / count($values);
    }
}
