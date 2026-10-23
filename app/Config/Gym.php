<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * CONFIG DI GYMLOG
 *
 * Tutte le "liste fisse" dell'app stanno qui, in un posto solo.
 * Le leggi ovunque con: config('Gym')->gruppiMuscolari
 */
class Gym extends BaseConfig
{
    /**
     * Gruppi muscolari selezionabili quando crei un esercizio.
     */
    public array $gruppiMuscolari = [
        'Petto',
        'Schiena',
        'Spalle',
        'Bicipiti',
        'Tricipiti',
        'Quadricipiti',
        'Femorali',
        'Glutei',
        'Polpacci',
        'Addome',
        'Full body',
    ];

    /**
     * Colori dei workout (chiave salvata a db => nome mostrato).
     * Il colore vero (hex) sta nel CSS: .disco--verde, .disco--blu, ...
     */
    public array $coloriWorkout = [
        'verde'   => 'Verde',
        'blu'     => 'Blu',
        'viola'   => 'Viola',
        'arancio' => 'Arancio',
        'rosa'    => 'Rosa',
        'ciano'   => 'Ciano',
    ];

    /**
     * Secondi di recupero proposti nel form "Crea workout".
     */
    public array $recuperi = [45, 60, 90, 120, 180, 240];

    /**
     * Periodi del filtro in Home (settimane => etichetta).
     */
    public array $periodi = [
        4  => 'Ultime 4 settimane',
        12 => 'Ultimi 3 mesi',
        26 => 'Ultimi 6 mesi',
    ];
}
