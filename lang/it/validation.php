<?php

/**
 * Messaggi di validazione in italiano (solo le regole che usiamo).
 * :attribute = nome del campo, :min / :max / :value = i numeri della regola.
 */
return [
    'array' => ':attribute non è valido.',
    'before_or_equal' => ':attribute non può essere nel futuro.',
    'between' => [
        'numeric' => ':attribute deve essere tra :min e :max.',
        'string' => ':attribute deve avere tra :min e :max caratteri.',
    ],
    'confirmed' => 'Le due password non coincidono.',
    'current_password' => 'La password attuale non è corretta.',
    'date' => ':attribute non è una data valida.',
    'email' => ':attribute deve essere un indirizzo email valido.',
    'image' => ':attribute deve essere un\'immagine.',
    'in' => ':attribute: scegli una delle opzioni.',
    'integer' => ':attribute deve essere un numero intero.',
    'max' => [
        'array' => 'Al massimo :max :attribute.',
        'file' => ':attribute può pesare al massimo :max KB.',
        'numeric' => ':attribute può essere al massimo :max.',
        'string' => ':attribute può avere al massimo :max caratteri.',
    ],
    'mimes' => ':attribute deve essere un file di tipo: :values.',
    'min' => [
        'array' => 'Serve almeno :min :attribute.',
        'numeric' => ':attribute deve essere almeno :min.',
        'string' => ':attribute deve avere almeno :min caratteri.',
    ],
    'numeric' => ':attribute deve essere un numero (es. 82.5).',
    'prohibited' => 'Richiesta non valida.',
    'required' => ':attribute è obbligatorio.',
    'string' => ':attribute non è valido.',
    'unique' => ':attribute è già usato.',
    'password' => [
        'min' => 'La password deve avere almeno :min caratteri.',
        'uncompromised' => 'Questa password è comparsa in un furto di dati: scegline un\'altra.',
    ],

    'attributes' => [
        'name' => 'nome',
        'email' => 'email',
        'password' => 'password',
        'current_password' => 'password attuale',
        'role' => 'ruolo',
        'date' => 'data',
        'notes' => 'note',
        'body_weight' => 'peso corporeo',
        'duration_minutes' => 'durata',
        'question' => 'domanda',
    ],
];
