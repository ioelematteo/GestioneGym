<?php
/**
 * LAYOUT PRINCIPALE: la "cornice" di tutte le pagine (sidebar, barra in alto, menu mobile).
 *
 * Ogni pagina fa:  <?= $this->extend('layouts/app') ?>
 * e riempie i "buchi" (sezioni) chiamati qui con renderSection():
 *   - contenuto -> il corpo della pagina
 *   - css / js  -> file extra che servono solo a quella pagina
 */
?>
<!doctype html>
<html lang="it">
<head>
    <?= $this->include('partials/head') ?>
    <?= $this->renderSection('css') ?>
</head>
<body>
    <div class="app">
        <?= $this->include('partials/sidebar') ?>

        <div class="app__corpo">
            <?= $this->include('partials/topbar') ?>

            <main class="app__main">
                <?= $this->include('partials/flash') ?>
                <?= $this->renderSection('contenuto') ?>
            </main>
        </div>
    </div>

    <?= $this->include('partials/nav_mobile') ?>

    <script src="<?= base_url('assets/js/app.js') ?>" defer></script>
    <?= $this->renderSection('js') ?>
</body>
</html>
