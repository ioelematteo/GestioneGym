<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * PROFILO: i tuoi dati e il cambio password.
 */
class Profilo extends BaseController
{
    public function index(): string
    {
        return view('profilo/index', [
            'titolo' => 'Profilo',
            'utente' => auth()->user(),
        ]);
    }

    public function password(): RedirectResponse
    {
        $regole = [
            'password_attuale'  => ['label' => 'Password attuale', 'rules' => 'required'],
            // strong_password = regola di Shield: blocca password corte, comuni o uguali al tuo nome/email
            'password'          => ['label' => 'Nuova password', 'rules' => 'required|min_length[8]|strong_password[]'],
            'password_conferma' => ['label' => 'Conferma password', 'rules' => 'required|matches[password]'],
        ];

        if (! $this->validate($regole)) {
            return $this->indietroConErrori($this->validator->getErrors());
        }

        $utente = auth()->user();

        // controllo che la password attuale sia giusta (senza rifare il login)
        $verifica = auth()->check([
            'email'    => $utente->email,
            'password' => $this->request->getPost('password_attuale'),
        ]);

        if (! $verifica->isOK()) {
            return $this->indietroConErrori(['password_attuale' => 'La password attuale non è corretta.']);
        }

        $utente->fill(['password' => $this->request->getPost('password')]);
        auth()->getProvider()->save($utente);

        return redirect()->to('profilo')->with('successo', 'Password aggiornata.');
    }
}
