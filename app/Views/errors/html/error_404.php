<?php
/**
 * PAGINA 404 (pagina non trovata) con lo stile di GymLog.
 * CodeIgniter la usa da sola quando una pagina non esiste
 * o quando un controller chiama $this->nonTrovato().
 */
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagina non trovata · GymLog</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/componenti.css') ?>">
    <style>
        body { display: grid; place-items: center; padding: 24px; text-align: center; }
        .numero-grande { font-family: var(--font-numeri); font-size: 64px; color: var(--accento); line-height: 1; }
        p { margin: 12px 0 24px; color: var(--testo-2); }
    </style>
</head>
<body>
    <main>
        <div class="numero-grande">404</div>
        <h1 class="titolo-pagina" style="margin-top: 16px">Qui non c'è niente</h1>
        <p>La pagina non esiste, oppure appartiene a un altro utente.</p>
        <a href="<?= base_url('/') ?>" class="btn btn--primario">Torna alla Home</a>
    </main>
</body>
</html>
