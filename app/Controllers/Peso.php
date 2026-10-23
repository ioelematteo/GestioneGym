<?php

namespace App\Controllers;

use App\Models\PesoCorporeoModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * PESO CORPOREO: salva la pesata dal form veloce in Home.
 */
class Peso extends BaseController
{
    public function salva(): RedirectResponse
    {
        $dati = [
            // accetto sia "56,8" che "56.8"
            'peso' => str_replace(',', '.', (string) $this->request->getPost('peso')),
            'data' => $this->request->getPost('data') ?: date('Y-m-d'),
        ];

        $regole = [
            'peso' => ['label' => 'Peso', 'rules' => 'required|decimal|greater_than[20]|less_than[400]'],
            'data' => ['label' => 'Data', 'rules' => 'required|valid_date[Y-m-d]'],
        ];

        if (! $this->validateData($dati, $regole)) {
            return $this->indietroConErrori($this->validator->getErrors());
        }

        model(PesoCorporeoModel::class)->registra($this->userId(), (float) $dati['peso'], $dati['data']);

        return redirect()->back()->with('successo', 'Peso salvato: ' . kg($dati['peso']) . ' kg.');
    }
}
