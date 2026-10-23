<?php
/**
 * PAGINA DI LOGIN
 * La logica è tutta di Shield (il suo LoginController): noi diamo solo l'aspetto.
 * L'abbiamo collegata in app/Config/Auth.php -> $views['login'].
 * Il campo anti-bot (honeypot) lo aggiunge da solo il filtro "honeypot".
 */
?>
<!doctype html>
<html lang="it">
<head>
    <?= componente('partials/head', ['titolo' => 'Accedi']) ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="pagina-auth">
    <main class="auth">
        <div class="auth__logo logo"><?= icona('bilanciere', 30) ?> GymLog</div>

        <section class="card auth__card">
            <h1 class="titolo-pagina">Accedi</h1>
            <p class="sottotitolo">Segna le serie, guarda i progressi.</p>

            <?php if (session('error') !== null): ?>
                <div class="flash flash--errore" role="alert"><?= icona('x', 18) ?> <span><?= esc(session('error')) ?></span></div>
            <?php elseif (session('errors') !== null): ?>
                <div class="flash flash--errore" role="alert">
                    <?= icona('x', 18) ?>
                    <span><?= esc(implode(' ', (array) session('errors'))) ?></span>
                </div>
            <?php endif ?>

            <?php if (session('message') !== null): ?>
                <div class="flash" role="status"><?= icona('check', 18) ?> <span><?= esc(session('message')) ?></span></div>
            <?php endif ?>

            <form action="<?= url_to('login') ?>" method="post" class="pila">
                <?= csrf_field() ?>

                <div class="campo">
                    <label class="campo__label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="input" inputmode="email" autocomplete="email" value="<?= old('email') ?>" required autofocus>
                </div>

                <div class="campo">
                    <label class="campo__label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="input" autocomplete="current-password" required>
                </div>

                <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <label class="spunta">
                        <input type="checkbox" name="remember" <?= old('remember') ? 'checked' : '' ?>> Ricordami su questo dispositivo
                    </label>
                <?php endif ?>

                <button type="submit" class="btn btn--primario btn--grande btn--blocco">Entra</button>
            </form>
        </section>

        <p class="auth__piede">Non hai un account? Chiedilo all'admin della palestra.</p>
    </main>
</body>
</html>
