<?php

namespace App\Controllers;

use App\Libraries\Dashboard;

/**
 * HOME: il riepilogo del tuo andamento.
 * Tutti i calcoli li fa la libreria Dashboard, qui chiediamo solo i pezzi.
 */
class Home extends BaseController
{
    public function index(): string
    {
        $settimane = $this->periodoScelto();
        $dashboard = new Dashboard($this->userId(), $settimane);

        return view('home/index', [
            'titolo'    => 'Home',
            'periodi'   => config('Gym')->periodi,
            'settimane' => $settimane,
            'cards'     => $dashboard->cards(),
            'frequenza' => $dashboard->frequenza(),
            'pesi'      => $dashboard->graficoPeso(),
            'ultimi'    => $dashboard->ultimiAllenamenti(),
            'progressi' => $dashboard->progressi(),
            'prossimi'  => $dashboard->prossimiWorkout(),
        ]);
    }

    /**
     * Legge il periodo dall'URL (?periodo=12). Se non è uno di quelli previsti -> 4 settimane.
     */
    private function periodoScelto(): int
    {
        $periodo = (int) $this->request->getGet('periodo');

        return array_key_exists($periodo, config('Gym')->periodi) ? $periodo : 4;
    }
}
