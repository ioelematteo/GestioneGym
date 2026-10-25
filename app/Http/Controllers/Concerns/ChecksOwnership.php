<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * "È TUO?" — se il record non appartiene all'utente loggato, 404.
 * Così nessuno apre i dati degli altri cambiando il numero nell'URL.
 */
trait ChecksOwnership
{
    protected function ensureOwns(Model $model): void
    {
        abort_unless((int) $model->user_id === (int) auth()->id(), 404);
    }
}
