<?php

use CodeIgniter\Router\RouteCollection;

/**
 * TUTTE LE ROTTE DI GYMLOG
 *
 * Come si legge una riga:
 *   $routes->get('workout/(:num)', 'Allenamento::esegui/$1');
 *   - get            = metodo HTTP (get = apro una pagina, post = invio un form)
 *   - 'workout/5'    = l'URL; (:num) accetta solo numeri
 *   - Allenamento    = il controller (app/Controllers/Allenamento.php)
 *   - esegui/$1      = il metodo del controller, $1 = il numero preso dall'URL
 *
 * @var RouteCollection $routes
 */

// Login / logout di Shield (registrazione e magic link disattivati: gli utenti li crea l'admin)
service('auth')->routes($routes, ['except' => ['register', 'magic-link']]);

// Tutto quello che sta qui dentro richiede il login (filtro "session" di Shield)
$routes->group('', ['filter' => 'session'], static function (RouteCollection $routes): void {
    // Home / dashboard
    $routes->get('/', 'Home::index');

    // Workout: scelgo cosa sto facendo e segno le serie
    $routes->get('workout', 'Allenamento::index');
    $routes->get('workout/(:num)', 'Allenamento::esegui/$1');
    $routes->post('workout/(:num)', 'Allenamento::salva/$1');

    // Crea / modifica / elimina un workout (la "scheda")
    $routes->get('workouts/nuovo', 'Workouts::nuovo');
    $routes->post('workouts', 'Workouts::crea');
    $routes->get('workouts/(:num)/modifica', 'Workouts::modifica/$1');
    $routes->post('workouts/(:num)', 'Workouts::aggiorna/$1');
    $routes->post('workouts/(:num)/elimina', 'Workouts::elimina/$1');

    // Storico allenamenti
    $routes->get('storico', 'Storico::index');
    $routes->get('storico/(:num)', 'Storico::dettaglio/$1');
    $routes->post('storico/(:num)/elimina', 'Storico::elimina/$1');

    // Peso corporeo (form veloce in Home)
    $routes->post('peso', 'Peso::salva');

    // Profilo (cambio password)
    $routes->get('profilo', 'Profilo::index');
    $routes->post('profilo/password', 'Profilo::password');
});

// Area admin: serve il login E il ruolo admin (filtro "group:admin" di Shield)
$routes->group('admin', ['filter' => ['session', 'group:admin']], static function (RouteCollection $routes): void {
    $routes->get('utenti', 'Admin\Utenti::index');
    $routes->get('utenti/nuovo', 'Admin\Utenti::nuovo');
    $routes->post('utenti', 'Admin\Utenti::crea');
    $routes->get('utenti/(:num)/modifica', 'Admin\Utenti::modifica/$1');
    $routes->post('utenti/(:num)', 'Admin\Utenti::aggiorna/$1');
    $routes->post('utenti/(:num)/blocco', 'Admin\Utenti::blocco/$1');
    $routes->post('utenti/(:num)/elimina', 'Admin\Utenti::elimina/$1');
});
