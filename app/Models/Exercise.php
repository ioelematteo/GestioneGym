<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Catalogo esercizi, condiviso tra gli utenti ("Panca inclinata" è sempre lo stesso). */
#[Fillable(['name'])]
class Exercise extends Model
{
    use HasFactory;

    /** Cerca per nome, se non c'è lo crea. */
    public static function findOrCreateByName(string $name): self
    {
        return static::firstOrCreate(['name' => trim($name)]);
    }
}
