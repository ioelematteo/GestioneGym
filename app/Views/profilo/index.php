<?= $this->extend('layouts/app') ?>

<?= $this->section('contenuto') ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina">Profilo</h1>
        <p class="sottotitolo">I tuoi dati di accesso.</p>
    </div>
</div>

<div class="griglia">
    <section class="card col-5">
        <h2 class="titolo-card">Account</h2>
        <dl class="dati">
            <div><dt>Nome</dt><dd><?= esc($utente->username) ?></dd></div>
            <div><dt>Email</dt><dd><?= esc($utente->email) ?></dd></div>
            <div><dt>Ruolo</dt><dd><?= $utente->inGroup('admin') ? 'Admin' : 'Utente' ?></dd></div>
            <div><dt>Iscritto dal</dt><dd><?= data_breve((string) $utente->created_at) ?></dd></div>
        </dl>
        <p class="campo__aiuto">Per cambiare nome o email chiedi a un admin.</p>
    </section>

    <section class="card col-7">
        <h2 class="titolo-card">Cambia password</h2>
        <form method="post" action="<?= site_url('profilo/password') ?>" class="pila form-stretto">
            <?= csrf_field() ?>

            <div class="campo">
                <label class="campo__label" for="password_attuale">Password attuale</label>
                <input type="password" id="password_attuale" name="password_attuale" class="input" autocomplete="current-password" required>
                <?= errore('password_attuale') ?>
            </div>

            <div class="form-riga">
                <div class="campo">
                    <label class="campo__label" for="password">Nuova password</label>
                    <input type="password" id="password" name="password" class="input" autocomplete="new-password" minlength="8" required>
                    <?= errore('password') ?>
                </div>
                <div class="campo">
                    <label class="campo__label" for="password_conferma">Ripeti la nuova password</label>
                    <input type="password" id="password_conferma" name="password_conferma" class="input" autocomplete="new-password" required>
                    <?= errore('password_conferma') ?>
                </div>
            </div>
            <p class="campo__aiuto">Almeno 8 caratteri, non troppo comune e senza il tuo nome o la tua email.</p>

            <div class="form-azioni">
                <button class="btn btn--primario">Aggiorna password</button>
            </div>
        </form>
    </section>
</div>

<?= $this->endSection() ?>
