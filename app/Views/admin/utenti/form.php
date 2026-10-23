<?= $this->extend('layouts/app') ?>

<?= $this->section('contenuto') ?>
<?php
$inModifica = $utente !== null;
$azione     = $inModifica ? 'admin/utenti/' . $utente->id : 'admin/utenti';
$ruolo      = old('ruolo', $inModifica && $utente->inGroup('admin') ? 'admin' : 'user');
?>

<a href="<?= site_url('admin/utenti') ?>" class="torna"><?= icona('freccia-sx', 16) ?> Utenti</a>

<div class="testata">
    <div>
        <h1 class="titolo-pagina"><?= $inModifica ? 'Modifica ' . esc($utente->username) : 'Nuovo utente' ?></h1>
        <p class="sottotitolo"><?= $inModifica ? 'Lascia la password vuota per non cambiarla.' : 'Comunica tu la password alla persona: potrà cambiarla dal suo Profilo.' ?></p>
    </div>
</div>

<section class="card form-card">
    <form method="post" action="<?= site_url($azione) ?>" class="pila">
        <?= csrf_field() ?>

        <div class="form-riga">
            <div class="campo">
                <label class="campo__label" for="username">Nome</label>
                <input id="username" name="username" class="input" value="<?= old('username', $utente->username ?? '') ?>" maxlength="30" required>
                <?= errore('username') ?>
            </div>
            <div class="campo">
                <label class="campo__label" for="email">Email</label>
                <input type="email" id="email" name="email" class="input" value="<?= old('email', $utente->email ?? '') ?>" required>
                <?= errore('email') ?>
            </div>
        </div>

        <div class="form-riga">
            <div class="campo">
                <label class="campo__label" for="password">Password<?= $inModifica ? ' <span class="testo-3">(facoltativa)</span>' : '' ?></label>
                <input type="password" id="password" name="password" class="input" autocomplete="new-password" minlength="8" <?= $inModifica ? '' : 'required' ?>>
                <?= errore('password') ?>
            </div>
            <div class="campo">
                <label class="campo__label" for="ruolo">Ruolo</label>
                <select id="ruolo" name="ruolo" class="input" <?= $sonoIo ? 'disabled' : '' ?>>
                    <?php foreach ($ruoli as $chiave => $info): ?>
                        <option value="<?= $chiave ?>" <?= $ruolo === $chiave ? 'selected' : '' ?>><?= esc($info['title']) ?> — <?= esc($info['description']) ?></option>
                    <?php endforeach ?>
                </select>
                <?php if ($sonoIo): ?>
                    <!-- un select disabilitato non viene inviato: mando il valore in un campo nascosto -->
                    <input type="hidden" name="ruolo" value="admin">
                    <p class="campo__aiuto">Non puoi cambiare il tuo ruolo.</p>
                <?php endif ?>
                <?= errore('ruolo') ?>
            </div>
        </div>

        <div class="form-azioni">
            <button class="btn btn--primario"><?= $inModifica ? 'Salva modifiche' : 'Crea utente' ?></button>
            <a href="<?= site_url('admin/utenti') ?>" class="btn btn--fantasma">Annulla</a>
        </div>
    </form>
</section>

<?= $this->endSection() ?>
