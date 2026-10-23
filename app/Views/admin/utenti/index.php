<?= $this->extend('layouts/app') ?>

<?= $this->section('contenuto') ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina">Utenti</h1>
        <p class="sottotitolo">Chi può entrare in GymLog e con che ruolo.</p>
    </div>
    <a href="<?= site_url('admin/utenti/nuovo') ?>" class="btn btn--primario"><?= icona('piu', 18) ?> Nuovo utente</a>
</div>

<section class="card">
    <table class="tabella tabella--utenti">
        <thead>
            <tr>
                <th>Utente</th>
                <th>Ruolo</th>
                <th>Stato</th>
                <th>Allenamenti</th>
                <th>Ultimo accesso</th>
                <th><span class="solo-lettori">Azioni</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utenti as $u): ?>
                <?php $sonoIo = (int) $u->id === (int) auth()->id(); ?>
                <tr>
                    <td data-label="Utente">
                        <div class="utente">
                            <span class="avatar"><?= esc(mb_strtoupper(mb_substr((string) $u->username, 0, 1))) ?></span>
                            <div>
                                <div class="riga__titolo"><?= esc($u->username) ?><?= $sonoIo ? ' <span class="testo-3">(tu)</span>' : '' ?></div>
                                <div class="riga__sotto"><?= esc($u->email) ?></div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Ruolo">
                        <span class="chip<?= $u->inGroup('admin') ? ' chip--accento' : '' ?>"><?= $u->inGroup('admin') ? 'Admin' : 'Utente' ?></span>
                    </td>
                    <td data-label="Stato">
                        <?php if ($u->isBanned()): ?>
                            <span class="stato stato--rosso">Bloccato</span>
                        <?php else: ?>
                            <span class="stato stato--ciano">Attivo</span>
                        <?php endif ?>
                    </td>
                    <td data-label="Allenamenti" class="numero"><?= (int) ($allenamenti[$u->id] ?? 0) ?></td>
                    <td data-label="Ultimo accesso" class="testo-2"><?= $u->last_active ? quando((string) $u->last_active) : 'mai' ?></td>
                    <td class="tabella__azioni">
                        <a href="<?= site_url('admin/utenti/' . $u->id . '/modifica') ?>" class="btn btn--icona btn--fantasma" aria-label="Modifica"><?= icona('matita', 17) ?></a>

                        <?php if (! $sonoIo): ?>
                            <form method="post" action="<?= site_url('admin/utenti/' . $u->id . '/blocco') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn--icona btn--fantasma" aria-label="<?= $u->isBanned() ? 'Sblocca' : 'Blocca' ?>" title="<?= $u->isBanned() ? 'Sblocca' : 'Blocca' ?>">
                                    <?= icona($u->isBanned() ? 'sblocca' : 'lucchetto', 17) ?>
                                </button>
                            </form>
                            <form method="post" action="<?= site_url('admin/utenti/' . $u->id . '/elimina') ?>"
                                  data-conferma="Eliminare <?= esc($u->username) ?>? Spariscono anche tutti i suoi workout e allenamenti.">
                                <?= csrf_field() ?>
                                <button class="btn btn--icona btn--fantasma testo-pericolo" aria-label="Elimina" title="Elimina"><?= icona('cestino', 17) ?></button>
                            </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
