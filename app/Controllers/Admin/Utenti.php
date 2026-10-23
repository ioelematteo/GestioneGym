<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SingoloWorkoutModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

/**
 * GESTIONE UTENTI (solo admin: lo decide il filtro "group:admin" in Routes.php)
 *
 * Gli utenti sono di Shield: li leggiamo e salviamo con il SUO model,
 * che si occupa da solo di email e password criptata.
 */
class Utenti extends BaseController
{
    public function index(): string
    {
        return view('admin/utenti/index', [
            'titolo'      => 'Utenti',
            'utenti'      => $this->utenti()->withIdentities()->withGroups()->orderBy('id')->findAll(),
            'allenamenti' => model(SingoloWorkoutModel::class)->contaPerUtente(),
        ]);
    }

    public function nuovo(): string
    {
        return $this->mostraForm(null);
    }

    public function crea(): RedirectResponse
    {
        if (! $this->validate($this->regole())) {
            return $this->indietroConErrori($this->validator->getErrors());
        }

        $utente = new User([
            'username' => trim((string) $this->request->getPost('username')),
            'email'    => trim((string) $this->request->getPost('email')),
            'password' => $this->request->getPost('password'),
        ]);
        $this->utenti()->save($utente);

        // ricarico l'utente per avere l'id, poi gli do il ruolo e lo attivo
        $utente = $this->utenti()->findById($this->utenti()->getInsertID());
        $utente->addGroup($this->request->getPost('ruolo'));
        $utente->activate();

        return redirect()->to('admin/utenti')->with('successo', "Utente {$utente->username} creato.");
    }

    public function modifica(int $id): string
    {
        return $this->mostraForm($this->utenteOppure404($id));
    }

    public function aggiorna(int $id): RedirectResponse
    {
        $utente = $this->utenteOppure404($id);

        if (! $this->validate($this->regole($id))) {
            return $this->indietroConErrori($this->validator->getErrors());
        }

        $utente->fill([
            'username' => trim((string) $this->request->getPost('username')),
            'email'    => trim((string) $this->request->getPost('email')),
        ]);

        // la password la cambio solo se l'hai scritta
        if ($this->request->getPost('password')) {
            $utente->fill(['password' => $this->request->getPost('password')]);
        }

        $this->utenti()->save($utente);

        // non puoi togliere il ruolo admin a te stesso (resteresti chiuso fuori)
        if (! $this->sonoIo($id)) {
            $utente->syncGroups($this->request->getPost('ruolo'));
        }

        return redirect()->to('admin/utenti')->with('successo', "Utente {$utente->username} aggiornato.");
    }

    /**
     * Blocca / sblocca un utente (un utente bloccato non può fare login).
     */
    public function blocco(int $id): RedirectResponse
    {
        $utente = $this->utenteOppure404($id);

        if ($this->sonoIo($id)) {
            return redirect()->back()->with('errore', 'Non puoi bloccare te stesso.');
        }

        if ($utente->isBanned()) {
            $utente->unBan();

            return redirect()->back()->with('successo', "{$utente->username} sbloccato.");
        }

        $utente->ban('Account bloccato dall\'amministratore.');

        return redirect()->back()->with('successo', "{$utente->username} bloccato.");
    }

    public function elimina(int $id): RedirectResponse
    {
        $utente = $this->utenteOppure404($id);

        if ($this->sonoIo($id)) {
            return redirect()->back()->with('errore', 'Non puoi eliminare te stesso.');
        }

        // true = cancellazione definitiva (con lui spariscono workout, allenamenti e pesate)
        $this->utenti()->delete($id, true);

        return redirect()->to('admin/utenti')->with('successo', "Utente {$utente->username} eliminato.");
    }

    // ---------------------------------------------------------------
    // Pezzi usati dai metodi sopra
    // ---------------------------------------------------------------

    private function mostraForm(?User $utente): string
    {
        return view('admin/utenti/form', [
            'titolo' => $utente ? 'Modifica utente' : 'Nuovo utente',
            'utente' => $utente,
            'ruoli'  => config('AuthGroups')->groups,
            'sonoIo' => $utente && $this->sonoIo($utente->id),
        ]);
    }

    /**
     * Regole del form. In modifica ($id) la password è facoltativa
     * e username/email possono restare uguali a quelli attuali.
     */
    private function regole(?int $id = null): array
    {
        $ignora = $id ? ",user_id,{$id}" : '';

        return [
            'username' => ['label' => 'Nome', 'rules' => 'required|min_length[3]|max_length[30]|alpha_numeric_punct|is_unique[users.username' . ($id ? ",id,{$id}" : '') . ']'],
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[254]|is_unique[auth_identities.secret' . $ignora . ']'],
            'password' => ['label' => 'Password', 'rules' => ($id ? 'permit_empty' : 'required') . '|min_length[8]|strong_password[]'],
            'ruolo'    => ['label' => 'Ruolo', 'rules' => 'required|in_list[' . implode(',', array_keys(config('AuthGroups')->groups)) . ']'],
        ];
    }

    private function utenti(): UserModel
    {
        return auth()->getProvider();
    }

    private function utenteOppure404(int $id): User
    {
        return $this->utenti()->findById($id) ?? $this->nonTrovato();
    }

    private function sonoIo(int|string $id): bool
    {
        return (int) $id === $this->userId();
    }
}
