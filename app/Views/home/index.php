<?= $this->extend('layouts/app') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('contenuto') ?>

<div class="testata">
    <div>
        <h1 class="titolo-pagina">Bentornato, <?= esc(nome_utente()) ?>.</h1>
        <p class="sottotitolo">Ecco il tuo andamento in sintesi.</p>
    </div>

    <!-- Filtro periodo: è un form GET, quindi cambia solo l'URL (?periodo=12) -->
    <form method="get" class="periodo">
        <label class="solo-lettori" for="periodo">Periodo</label>
        <span class="periodo__icona"><?= icona('calendario', 18) ?></span>
        <select id="periodo" name="periodo" class="input" data-invia-al-cambio>
            <?php foreach ($periodi as $valore => $testo): ?>
                <option value="<?= $valore ?>" <?= $valore === $settimane ? 'selected' : '' ?>><?= esc($testo) ?></option>
            <?php endforeach ?>
        </select>
        <noscript><button class="btn btn--piccolo">Applica</button></noscript>
    </form>
</div>

<div class="griglia">

    <!-- ====== 4 NUMERI ====== -->
    <div class="col-3">
        <?= componente('partials/stat_card', [
            'icona'  => 'bilanciere',
            'label'  => 'Allenamenti',
            'valore' => $cards['allenamenti']['valore'],
            'piede'  => trend($cards['allenamenti']['delta']) . '<span>vs periodo prima</span>',
        ]) ?>
    </div>

    <div class="col-3">
        <?= componente('partials/stat_card', [
            'icona'  => 'barre',
            'label'  => 'Media settimanale',
            'valore' => kg($cards['media']['settimanale']),
            'unita'  => '/ sett.',
            'piede'  => trend($cards['media']['delta']) . '<span>≈ ' . kg($cards['media']['mensile']) . ' al mese</span>',
        ]) ?>
    </div>

    <div class="col-3">
        <?= componente('partials/stat_card', [
            'icona'   => 'bilancia',
            'label'   => 'Peso corporeo',
            'valore'  => $cards['peso']['valore'] !== null ? kg($cards['peso']['valore']) : '—',
            'unita'   => $cards['peso']['valore'] !== null ? 'kg' : '',
            'piede'   => trend($cards['peso']['delta'], ' kg', neutro: true),
            'grafico' => sparkline($cards['peso']['valori']),
        ]) ?>
    </div>

    <div class="col-3">
        <?= componente('partials/stat_card', [
            'icona'   => 'strati',
            'label'   => 'Volume totale',
            'valore'  => volume_breve($cards['volume']['valore']),
            'unita'   => 'kg',
            'piede'   => trend($cards['volume']['delta'], '%'),
            'grafico' => mini_barre($cards['volume']['valori']),
        ]) ?>
    </div>

    <!-- ====== GRAFICO FREQUENZA ====== -->
    <section class="card col-8">
        <div class="card__testa">
            <div>
                <h2 class="titolo-card">Frequenza allenamenti</h2>
                <p class="card__sotto" data-sottotitolo-frequenza>Numero di allenamenti per settimana</p>
            </div>
            <div class="segmenti" role="group" aria-label="Raggruppa per">
                <button type="button" aria-pressed="true" data-frequenza="settimane">Settimane</button>
                <button type="button" aria-pressed="false" data-frequenza="mesi">Mesi</button>
            </div>
        </div>
        <div class="grafico grafico--frequenza">
            <canvas id="grafico-frequenza" role="img" aria-label="Allenamenti per settimana"></canvas>
        </div>
    </section>

    <!-- ====== GRAFICO PESO + PESATA VELOCE ====== -->
    <section class="card col-4 card-peso">
        <div class="card__testa">
            <div>
                <h2 class="titolo-card">Peso corporeo</h2>
                <p class="card__sotto">Ultimi 3 mesi</p>
            </div>
        </div>

        <?php if (count($pesi) >= 2): ?>
            <div class="grafico grafico--peso">
                <canvas id="grafico-peso" role="img" aria-label="Andamento del peso corporeo"></canvas>
            </div>
        <?php else: ?>
            <?= componente('partials/vuoto', [
                'icona'  => 'bilancia',
                'titolo' => 'Servono almeno 2 pesate',
                'testo'  => 'Segna il tuo peso qui sotto, il grafico arriva da solo.',
            ]) ?>
        <?php endif ?>

        <form action="<?= site_url('peso') ?>" method="post" class="pesata">
            <?= csrf_field() ?>
            <label class="solo-lettori" for="peso">Peso di oggi in kg</label>
            <input id="peso" name="peso" class="input numero" inputmode="decimal" placeholder="Peso di oggi (kg)" required>
            <button class="btn btn--piccolo"><?= icona('piu', 16) ?> Salva</button>
        </form>
        <?= errore('peso') ?>
    </section>

    <!-- ====== ULTIMI ALLENAMENTI ====== -->
    <section class="card col-4">
        <div class="card__testa">
            <h2 class="titolo-card">Ultimi allenamenti</h2>
            <a href="<?= site_url('storico') ?>" class="link-tutti">Vedi tutti <?= icona('freccia-dx', 16) ?></a>
        </div>

        <?php if ($ultimi === []): ?>
            <?= componente('partials/vuoto', [
                'icona'   => 'storico',
                'titolo'  => 'Ancora nessun allenamento',
                'testo'   => 'Quando ne salvi uno, lo trovi qui.',
                'link'    => 'workout',
                'bottone' => 'Inizia ad allenarti',
            ]) ?>
        <?php else: ?>
            <div class="righe">
                <?php foreach ($ultimi as $a): ?>
                    <?= componente('partials/riga_allenamento', ['a' => $a]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>

    <!-- ====== PROGRESSI PRINCIPALI ====== -->
    <section class="card col-4">
        <div class="card__testa">
            <div>
                <h2 class="titolo-card">Progressi principali</h2>
                <p class="card__sotto">Serie migliore: ultima volta vs inizio periodo</p>
            </div>
        </div>

        <?php if ($progressi === []): ?>
            <?= componente('partials/vuoto', [
                'icona'  => 'su',
                'titolo' => 'Nessun confronto ancora',
                'testo'  => 'Fai lo stesso esercizio almeno 2 volte nel periodo.',
            ]) ?>
        <?php else: ?>
            <div class="righe">
                <?php foreach ($progressi as $p): ?>
                    <div class="riga progresso">
                        <span class="riga__testo">
                            <span class="riga__titolo"><?= esc($p['esercizio']) ?></span>
                            <span class="riga__sotto"><?= $p['volte'] ?> volte nel periodo</span>
                        </span>
                        <span class="progresso__numeri">
                            <span class="riga__valore"><?= serie_testo($p['ultima']['peso'], $p['ultima']['rep']) ?></span>
                            <span class="progresso__prima">Prima: <?= serie_testo($p['prima']['peso'], $p['prima']['rep']) ?></span>
                        </span>
                        <?= trend($p['variazione'], '%') ?>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>

    <!-- ====== PROSSIMI WORKOUT ====== -->
    <section class="card col-4">
        <div class="card__testa">
            <div>
                <h2 class="titolo-card">Prossimi workout</h2>
                <p class="card__sotto">Prima quello che non fai da più tempo</p>
            </div>
            <a href="<?= site_url('workout') ?>" class="link-tutti">Vedi tutti <?= icona('freccia-dx', 16) ?></a>
        </div>

        <?php if ($prossimi === []): ?>
            <?= componente('partials/vuoto', [
                'icona'   => 'piu',
                'titolo'  => 'Nessun workout',
                'testo'   => 'Crea la tua prima scheda: Push, Pull, Legs, quello che vuoi.',
                'link'    => 'workouts/nuovo',
                'bottone' => 'Crea workout',
            ]) ?>
        <?php else: ?>
            <div class="pila pila--stretta">
                <?php foreach ($prossimi as $i => $w): ?>
                    <?= componente('partials/card_workout', ['w' => $w, 'consigliato' => $i === 0]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>
</div>

<!-- I dati dei grafici, in JSON: li legge assets/js/home.js -->
<script type="application/json" id="dati-grafici"><?= json_encode([
    'frequenza' => $frequenza,
    'pesi'      => $pesi,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="<?= base_url('assets/vendor/chart.umd.js') ?>" defer></script>
<script src="<?= base_url('assets/js/home.js') ?>" defer></script>
<?= $this->endSection() ?>
