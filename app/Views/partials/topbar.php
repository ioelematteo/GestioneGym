<header class="topbar">
    <a href="<?= site_url('/') ?>" class="logo"><?= icona('bilanciere', 24) ?> GymLog</a>

    <!-- <details> è un elemento HTML che si apre/chiude da solo al click: niente JavaScript -->
    <details class="menu-utente">
        <summary>
            <span class="avatar"><?= esc(mb_strtoupper(mb_substr(nome_utente(), 0, 1))) ?></span>
            <span class="menu-utente__nome">
                <?= esc(nome_utente()) ?>
                <span class="menu-utente__ruolo"><?= is_admin() ? 'Admin' : 'Utente' ?></span>
            </span>
            <?= icona('chevron-giu', 16) ?>
        </summary>

        <div class="menu-utente__tendina">
            <a href="<?= site_url('profilo') ?>"><?= icona('utente', 18) ?> Profilo</a>
            <?php if (is_admin()): ?>
                <a href="<?= site_url('admin/utenti') ?>"><?= icona('utenti', 18) ?> Gestione utenti</a>
            <?php endif ?>
            <a href="<?= site_url('logout') ?>"><?= icona('esci', 18) ?> Esci</a>
        </div>
    </details>
</header>
