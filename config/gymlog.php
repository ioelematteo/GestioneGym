<?php

/**
 * CONFIGURAZIONE DI GYMLOG (le liste fisse e le impostazioni dell'app).
 * Le leggi con: config('gymlog.colors')
 */
return [

    // Colori delle schede = colori dei dischi olimpici (chiave => etichetta)
    'colors' => [
        'rosso' => 'Rosso · 25 kg',
        'blu' => 'Blu · 20 kg',
        'giallo' => 'Giallo · 15 kg',
        'verde' => 'Verde · 10 kg',
        'bianco' => 'Bianco · 5 kg',
        'nero' => 'Nero',
    ],

    // Secondi di recupero proposti
    'rest_options' => [45, 60, 90, 120, 150, 180, 240],

    // Filtro periodo della dashboard (settimane => etichetta)
    'periods' => [
        4 => 'Ultime 4 settimane',
        12 => 'Ultimi 3 mesi',
        26 => 'Ultimi 6 mesi',
    ],

    // Dove salvare le foto dei progressi (su Laravel Cloud: il disco del bucket, es. "s3")
    'photos_disk' => env('GYMLOG_PHOTOS_DISK', 'local'),

    // Coach IA
    'coach' => [
        'weeks_of_data' => 6, // quante settimane di dati mandare all'IA
    ],
];
