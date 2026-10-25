<?php

namespace App\Services\Body;

use App\Models\BodyEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * PESO CORPOREO E MISURE: una misurazione al giorno.
 * Se registri due volte lo stesso giorno, la seconda aggiorna la prima.
 */
class BodyLog
{
    /** @param array{measured_on: string, weight_kg: float, body_fat: ?float, waist_cm: ?float, notes: ?string} $data */
    public function record(User $user, array $data): BodyEntry
    {
        // whereDate: confronta solo il giorno (alcuni database salvano anche l'ora 00:00:00)
        $entry = $user->bodyEntries()->whereDate('measured_on', $data['measured_on'])->first()
            ?? $user->bodyEntries()->make(['measured_on' => $data['measured_on']]);

        $entry->fill(collect($data)->except('measured_on')->all())->save();

        return $entry;
    }

    /** @return Collection<int, BodyEntry> dalla più vecchia alla più nuova */
    public function since(User $user, CarbonImmutable $from): Collection
    {
        return $user->bodyEntries()->where('measured_on', '>=', $from->toDateString())->orderBy('measured_on')->get();
    }

    /** @return Collection<int, BodyEntry> le ultime N, dalla più vecchia alla più nuova */
    public function latest(User $user, int $count): Collection
    {
        return $user->bodyEntries()->latest('measured_on')->take($count)->get()->reverse()->values();
    }
}
