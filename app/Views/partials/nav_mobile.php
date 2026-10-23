<nav class="nav-mobile" aria-label="Menu">
    <a href="<?= site_url('/') ?>" class="<?= url_is('/') ? 'is-attiva' : '' ?>"><?= icona('home', 22) ?> Home</a>
    <a href="<?= site_url('workout') ?>" class="<?= url_is('workout') || url_is('workout/*') ? 'is-attiva' : '' ?>"><?= icona('bilanciere', 22) ?> Workout</a>
    <a href="<?= site_url('workouts/nuovo') ?>" class="<?= url_is('workouts*') ? 'is-attiva' : '' ?>"><?= icona('piu', 22) ?> Crea</a>
    <a href="<?= site_url('storico') ?>" class="<?= url_is('storico*') ? 'is-attiva' : '' ?>"><?= icona('storico', 22) ?> Storico</a>
</nav>
