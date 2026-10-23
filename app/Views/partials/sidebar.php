<?php
// url_is('workout*') = "l'URL attuale inizia con workout?" -> serve per evidenziare la voce attiva
$voci = [
    ['url' => '/',              'icona' => 'home',       'testo' => 'Home',        'attiva' => url_is('/')],
    ['url' => 'workout',        'icona' => 'bilanciere', 'testo' => 'Workout',     'attiva' => url_is('workout') || url_is('workout/*')],
    ['url' => 'workouts/nuovo', 'icona' => 'piu',        'testo' => 'Crea Workout', 'attiva' => url_is('workouts*')],
    ['url' => 'storico',        'icona' => 'storico',    'testo' => 'Storico',     'attiva' => url_is('storico*')],
];
?>
<aside class="sidebar">
    <a href="<?= site_url('/') ?>" class="logo"><?= icona('bilanciere', 26) ?> GymLog</a>

    <nav class="sidebar__nav" aria-label="Menu principale">
        <?php foreach ($voci as $voce): ?>
            <a href="<?= site_url($voce['url']) ?>" class="voce-nav<?= $voce['attiva'] ? ' is-attiva' : '' ?>" <?= $voce['attiva'] ? 'aria-current="page"' : '' ?>>
                <?= icona($voce['icona']) ?> <?= $voce['testo'] ?>
            </a>
        <?php endforeach ?>

        <?php if (is_admin()): ?>
            <div class="sidebar__divisore"></div>
            <a href="<?= site_url('admin/utenti') ?>" class="voce-nav<?= url_is('admin/utenti*') ? ' is-attiva' : '' ?>">
                <?= icona('utenti') ?> Utenti
                <span class="voce-nav__freccia"><?= icona('chevron-dx', 16) ?></span>
            </a>
        <?php endif ?>
    </nav>
</aside>
