<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

/**
 * I due utenti iniziali.
 * CAMBIA LE PASSWORD dopo il primo login (pagina Profilo).
 */
class UtentiSeeder extends Seeder
{
    public function run(): void
    {
        $this->creaUtente('Matteo', 'ioelematteo@gmail.com', 'Matteo2026!', 'admin');
        $this->creaUtente('Utente', 'user@test.it', 'User2026!', 'user');
    }

    private function creaUtente(string $username, string $email, string $password, string $ruolo): void
    {
        // auth()->getProvider() = il model degli utenti di Shield
        $users = auth()->getProvider();

        if ($users->findByCredentials(['email' => $email]) !== null) {
            echo "  - {$email} esiste già, salto.\n";

            return;
        }

        // Shield salva da solo l'email e la password (criptata) in auth_identities
        $user = new User([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ]);
        $users->save($user);

        // ricarico l'utente appena creato per avere l'id
        $user = $users->findById($users->getInsertID());
        $user->addGroup($ruolo);
        $user->activate();

        echo "  + creato {$email} ({$ruolo})\n";
    }
}
