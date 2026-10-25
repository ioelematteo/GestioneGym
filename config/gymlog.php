<?php

/**
 * CONFIGURAZIONE DI GYMLOG (le liste fisse e le impostazioni dell'app).
 * Le leggi con: config('gymlog.colors')
 */
return [

    // Colori delle schede (chiave => etichetta). Le chiavi corrispondono ai token
    // --color-w-* in resources/css/app.css
    'colors' => [
        'verde' => 'Verde',
        'blu' => 'Blu',
        'viola' => 'Viola',
        'arancio' => 'Arancio',
        'rosa' => 'Rosa',
        'ciano' => 'Ciano',
        'rosso' => 'Rosso',
        'giallo' => 'Giallo',
    ],

    // Secondi di recupero proposti
    'rest_options' => [45, 60, 90, 120, 150, 180, 240],

    // Filtro periodo della dashboard (settimane => etichetta)
    'periods' => [
        4 => '4 settimane',
        12 => '3 mesi',
        26 => '6 mesi',
    ],

    // Dove salvare le foto dei progressi (su Laravel Cloud: il disco del bucket, es. "s3")
    'photos_disk' => env('GYMLOG_PHOTOS_DISK', 'local'),

    // Coach IA
    'coach' => [
        'weeks_of_data' => 6, // quante settimane di dati mandare all'IA
    ],
];
