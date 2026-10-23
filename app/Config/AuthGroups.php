<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

/**
 * RUOLI E PERMESSI (l'equivalente di Spatie in Laravel)
 *
 * - $groups      = i "ruoli" (in Shield si chiamano gruppi)
 * - $permissions = le singole azioni che si possono fare
 * - $matrix      = quale ruolo ha quali permessi
 *
 * Per controllare nel codice:
 *   auth()->user()->inGroup('admin')          -> è admin?
 *   auth()->user()->can('utenti.gestisci')    -> ha il permesso?
 */
class AuthGroups extends ShieldAuthGroups
{
    /**
     * Ruolo assegnato in automatico a un nuovo utente.
     */
    public string $defaultGroup = 'user';

    public array $groups = [
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Può gestire gli utenti.',
        ],
        'user' => [
            'title'       => 'Utente',
            'description' => 'Usa l\'app per i suoi allenamenti.',
        ],
    ];

    public array $permissions = [
        'utenti.gestisci' => 'Creare, modificare, bloccare ed eliminare utenti',
    ];

    public array $matrix = [
        'admin' => ['utenti.gestisci'],
        'user'  => [],
    ];
}
