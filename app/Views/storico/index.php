<?= $this->extend('layouts/app') ?>

<?= $this->section('contenuto') ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina">Storico</h1>
        <p class="sottotitolo">Tutti i tuoi allenamenti, dal più recente.</p>
    </div>
</div>

<section class="card">
    <?php if ($allenamenti === []): ?>
        <?= componente('partials/vuoto', [
            'icona'   => 'storico',
            'titolo'  => 'Lo storico è vuoto',
            'testo'   => 'Ogni allenamento che salvi finisce qui, con tutte le serie.',
            'link'    => 'workout',
            'bottone' => 'Inizia un workout',
        ]) ?>
    <?php else: ?>
        <div class="righe">
            <?php foreach ($allenamenti as $a): ?>
                <?= componente('partials/riga_allenamento', ['a' => $a]) ?>
            <?php endforeach ?>
        </div>

        <!-- links('default', 'gym') = usa il template "gym" registrato in app/Config/Pager.php -->
        <?= $pager->links('default', 'gym') ?>
    <?php endif ?>
</section>

<?= $this->endSection() ?>
