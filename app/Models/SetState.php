<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Stato di una serie: "Ultime kippate", "Ultima mezza rep", "Spottato"... */
#[Fillable(['name', 'description', 'color', 'position'])]
class SetState extends Model
{
    public $timestamps = false;
}
