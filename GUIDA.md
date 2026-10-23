# 🏋️ GymLog — la guida

> **In una riga:** una piccola webapp in CodeIgniter 4 per segnare gli allenamenti in palestra
> e vedere come stai andando.
>
> **Come leggere questa guida:** ogni titolo con la ▸ è una **scheda chiusa**. Clicca per aprirla.
> Dentro ci sono altre schede. Apri solo quello che ti serve adesso, il resto può aspettare.

---

<details>
<summary><b>⚡ Avvio rapido (5 minuti)</b></summary>

<br>

**Ti serve:** PHP 8.2+, Composer, MySQL/MariaDB (XAMPP, Laragon, MAMP vanno bene).

```bash
# 1. scarica le librerie (crea la cartella vendor/)
composer install

# 2. crea il file di configurazione e modificalo (nome database, utente, password)
cp .env.example .env

# 3. genera la chiave segreta dell'app
php spark key:generate

# 4. crea il database "gymlog" nel tuo MySQL (da phpMyAdmin o da terminale), poi:
php spark migrate --all

# 5. riempi le tabelle fisse e crea i 2 utenti
php spark db:seed DatabaseSeeder

# 6. (FACOLTATIVO) dati finti per vedere i grafici pieni
php spark db:seed DemoSeeder

# 7. accendi il server
php spark serve
```

Apri **http://localhost:8080**

| Utente | Email | Password | Ruolo |
|---|---|---|---|
| Matteo | `ioelematteo@gmail.com` | `Matteo2026!` | admin |
| Utente | `user@test.it` | `User2026!` | user |

> ⚠️ Cambia le password dopo il primo accesso: menu in alto a destra → **Profilo**.

<details>
<summary>Cosa fa <code>--all</code> in <code>migrate --all</code>?</summary>

<br>

Le migration non sono solo nostre: anche **Shield** (il login) e **Settings** hanno le loro tabelle.
`--all` dice: "esegui le migration di TUTTI i pacchetti, non solo quelle in `app/`".

</details>

<details>
<summary>E se non ho MySQL? Posso usare SQLite?</summary>

<br>

Sì. Il codice usa solo il Query Builder di CodeIgniter (niente SQL specifico di MySQL),
quindi funziona anche con SQLite. Nel `.env` metti:

```ini
database.default.database = gymlog.db
database.default.DBDriver = SQLite3
```

Il file `gymlog.db` verrà creato dentro `writable/`.

</details>

</details>

---

<details>
<summary><b>🗺️ Cosa fa l'app (le pagine)</b></summary>

<br>

| Pagina | URL | A cosa serve |
|---|---|---|
| Home | `/` | Riepilogo: numeri, grafici, progressi |
| Workout | `/workout` | Scegli cosa alleni e segni le serie **mentre sei in palestra** |
| Crea Workout | `/workouts/nuovo` | Crei una scheda (Push A, Pull B, Legs…) |
| Storico | `/storico` | Tutti gli allenamenti fatti |
| Profilo | `/profilo` | Cambi la tua password |
| Utenti | `/admin/utenti` | Solo admin: crea, modifica, blocca, elimina utenti |

<details>
<summary><b>🏠 Home</b></summary>

<br>

In alto c'è il filtro periodo: **4 settimane / 3 mesi / 6 mesi**.
Cambia l'URL in `/?periodo=12` (12 = settimane).

<details>
<summary>Le 4 card con i numeri</summary>

<br>

| Card | Cosa mostra | Confronto |
|---|---|---|
| Allenamenti | quanti ne hai fatti nel periodo | vs periodo precedente (stessa durata) |
| Media settimanale | allenamenti / settimane, e la stima al mese | vs periodo precedente |
| Peso corporeo | ultima pesata + mini grafico | quanto è cambiato nel periodo |
| Volume totale | somma di **kg × rep** di tutte le serie | % vs periodo precedente |

- La freccia verde ↑ = meglio, rossa ↓ = peggio.
- Il **peso corporeo** ha la pillola grigia: salire di peso può essere bene o male, dipende dal tuo obiettivo. L'app non giudica.

</details>

<details>
<summary>Grafico "Frequenza allenamenti"</summary>

<br>

- **Settimane:** quanti allenamenti in ognuna delle ultime 8 settimane (da lunedì a domenica).
- **Mesi:** quanti allenamenti in ognuno degli ultimi 6 mesi.
- L'ultima barra (settimana/mese in corso) è più accesa.

</details>

<details>
<summary>Grafico "Peso corporeo" + pesata veloce</summary>

<br>

- Le pesate degli ultimi 3 mesi.
- Sotto c'è un campo: scrivi il peso di oggi e premi **Salva**.
- Se pesi due volte nello stesso giorno, **la seconda sostituisce la prima** (una pesata al giorno).
- Puoi scrivere sia `56,8` che `56.8`.

</details>

<details>
<summary>"Progressi principali"</summary>

<br>

Per ogni esercizio fatto almeno 2 volte nel periodo:

1. prendo la **serie migliore dell'ultima volta**
2. la confronto con la **serie migliore della prima volta nel periodo**
3. mostro la % di miglioramento

"Principali" = i 5 esercizi che hai fatto **più spesso**.

<details>
<summary>Come capisco qual è la "serie migliore"? (80×6 è meglio di 85×4?)</summary>

<br>

Uso il **massimale stimato** (formula di Epley): `peso × (1 + rep / 30)`

- 80 × 6 → 80 × 1.2 = **96**
- 85 × 4 → 85 × 1.133 = **96.3**

Quindi 85×4 vince di poco. A corpo libero (peso 0) conto solo le rep.
La funzione è `Statistiche::punteggioSerie()`.

</details>

</details>

<details>
<summary>"Prossimi workout"</summary>

<br>

I tuoi workout ordinati per **"quale tocca adesso"**:
prima quelli mai fatti, poi quello che non fai da più tempo.
Il primo ha l'etichetta **"Tocca a questo"**. Il ▶ ti porta dritto alla pagina Workout.

</details>

</details>

<details>
<summary><b>🏋️ Workout (la pagina da usare in palestra)</b></summary>

<br>

1. Vai su **Workout** e scegli la scheda (es. Push A).
2. Vedi tutti gli esercizi con le serie da fare.
3. Per ogni serie scrivi **kg** e **rep**, e se serve uno **stato**.
4. In fondo premi **Salva allenamento**.

<details>
<summary>I "numeri fantasma" (placeholder dell'ultima volta)</summary>

<br>

Dentro i campi vuoti vedi in grigio i numeri dell'**ultima volta che hai fatto questo workout**.
Sotto ogni serie c'è anche scritto: `↺ ultima 80 kg × 6 · Spottato`.

- **Tocca quella scritta** → copia peso e rep nei campi.
- Se l'ultima volta avevi fatto più serie del previsto, le righe in più compaiono da sole.

<details>
<summary>Da dove arrivano questi numeri? (il codice)</summary>

<br>

È esattamente la tua idea:

> "da `singolo_workout` prendi per ogni fk a workout l'ultimo registrato di data"

```php
// app/Models/SingoloWorkoutModel.php
public function ultimoPerWorkout(int $userId, int $workoutId): ?array
{
    return $this->where('user_id', $userId)
        ->where('workout_id', $workoutId)
        ->orderBy('data_esecuzione', 'DESC') // il più recente per primo
        ->orderBy('id', 'DESC')              // stessa data? vince l'ultimo salvato
        ->first();                           // ne prendo solo uno
}
```

Poi `SerieEseguitaModel::perEsercizioESerie()` organizza le sue serie così:

```
[esercizio_id => [numero_serie => ['peso' => 80, 'rep' => 6, ...]]]
```

…e la vista fa `$ultime[$esercizioId][$numeroSerie]` per ogni riga.

</details>

</details>

<details>
<summary>Il bottone ✓ (serie fatta) e il timer di recupero</summary>

<br>

- Premi **✓** quando finisci una serie.
- Se i campi sono **vuoti**, copia da solo i numeri dell'ultima volta (hai rifatto uguale? un tocco e basta).
- Parte il **timer di recupero** in basso, con i secondi impostati nella scheda (es. 2:00).
- Bottoni del timer: **+15s** e **Salta**. Alla fine il telefono **vibra** (se il browser lo permette).

</details>

<details>
<summary>Gli stati della serie</summary>

<br>

Ogni serie può avere uno stato (tabella `stati`):

| Stato | Quando usarlo |
|---|---|
| Ultime kippate | le ultime rep con slancio |
| Ultima mezza rep | l'ultima rep non completa |
| Spottato | qualcuno ti ha aiutato |
| Cedimento | arrivato a cedimento |
| Rest-pause | piccole pause dentro la serie |
| Fastidio | dolore o fastidio |

Di default è **"Pulita"** (= nessuno stato).

</details>

<details>
<summary>La bozza: se chiudi la pagina non perdi niente</summary>

<br>

Mentre scrivi, tutto viene salvato **nel browser del telefono** (`localStorage`).
Se la pagina si ricarica, il telefono si blocca o chiudi per sbaglio:

- riapri lo stesso workout → ritrovi tutto, anche il cronometro
- compare l'avviso **"Ho ripreso l'allenamento che avevi lasciato a metà"**
- **Ricomincia da zero** cancella la bozza

La bozza si cancella da sola dopo il salvataggio, e viene ignorata se è più vecchia di 12 ore.

</details>

<details>
<summary>"Chiudi allenamento": data, peso corporeo, note</summary>

<br>

- **Data:** di default oggi. Puoi mettere un giorno passato se ti eri dimenticato di segnarlo.
- **Peso corporeo:** facoltativo, finisce nel grafico del peso.
- **Note:** come è andata. La rivedi in cima la prossima volta che fai lo stesso workout.
- **Durata:** la prende dal cronometro in alto (se la data è oggi).
- Si salvano **solo le serie con le rep scritte**. Righe vuote = serie non fatta.

Dopo il salvataggio vai al riepilogo dell'allenamento. **Quell'allenamento diventa "l'ultimo"**, ma i vecchi restano nello storico.

</details>

</details>

<details>
<summary><b>➕ Crea Workout</b></summary>

<br>

- **Nome**, **descrizione** (facoltativa), **colore**.
- Esercizi: nome, gruppo muscolare, serie, rep obiettivo (es. `8-10`), recupero, note.
- Scrivendo il nome compaiono i **suggerimenti** dal catalogo. Se scegli un esercizio esistente, il gruppo muscolare si riempie da solo.
- Esercizio **nuovo**? Lo crea in automatico nel catalogo.
- **↑ ↓** cambiano l'ordine, **✕** toglie la riga.

<details>
<summary>La lettera nel disco colorato</summary>

<br>

`Push A` → **A**, `Pull B` → **B**, `Legs` → **L**, `Giorno 2` → **2**.
Regola: se l'ultima parola è lunga 1-2 caratteri uso quella, sennò la prima lettera.
Funzione PHP: `iniziale_workout()` (con la sua gemella in JS per l'anteprima).

</details>

<details>
<summary>Cosa succede se modifico o elimino un workout?</summary>

<br>

- **Modifico:** gli esercizi vecchi della scheda vengono sostituiti. Lo storico **non si rompe**, perché le serie fatte puntano all'**esercizio** (`esercizi.id`), non alla riga della scheda.
- **Elimino:** è un *soft delete*: il workout sparisce dalle liste, ma nel database resta (con `deleted_at` pieno). Così lo storico sa ancora come si chiamava.

</details>

</details>

<details>
<summary><b>🕓 Storico</b></summary>

<br>

- Lista di tutti gli allenamenti (12 per pagina).
- Aprendone uno vedi: durata, volume, numero serie, ogni serie con il suo stato.
- **Record personale:** compare quando la serie migliore di quel giorno batte **tutte** quelle fatte prima per quell'esercizio.
- **Rifallo** → apre lo stesso workout.
- **Elimina** → per gli allenamenti salvati per sbaglio.

</details>

<details>
<summary><b>👥 Utenti (solo admin)</b></summary>

<br>

- Crea utenti (nome, email, password, ruolo).
- Modifica (password facoltativa: se la lasci vuota non cambia).
- **Blocca / sblocca:** un utente bloccato non riesce a fare login.
- **Elimina:** cancella l'utente **e tutti i suoi dati** (workout, allenamenti, pesate).
- Non puoi bloccare, eliminare o togliere il ruolo admin **a te stesso** (resteresti chiuso fuori).

La registrazione pubblica è **spenta**: gli account li crea l'admin. Meno porte aperte = meno bot.

</details>

</details>

---

<details>
<summary><b>🧱 Architettura (come è fatto dentro)</b></summary>

<br>

**TL;DR:** classico **MVC** di CodeIgniter. Il Controller riceve la richiesta, chiede i dati ai Model, li passa alla View.
I calcoli "pesanti" stanno in **Libraries**, le funzioni di formattazione in **Helpers**.

<details>
<summary>La mappa delle cartelle</summary>

<br>

```
app/
├── Config/
│   ├── Routes.php        ← tutti gli URL dell'app
│   ├── Filters.php       ← anti-bot: honeypot, CSRF, limite login
│   ├── Auth.php          ← impostazioni di Shield (login)
│   ├── AuthGroups.php    ← ruoli admin / user e permessi
│   └── Gym.php           ← le NOSTRE liste fisse (gruppi muscolari, colori, periodi...)
├── Controllers/          ← ricevono la richiesta, rispondono con una pagina
│   ├── BaseController.php    ← il "padre": userId(), indietroConErrori(), nonTrovato()
│   ├── Home.php
│   ├── Allenamento.php       ← pagina Workout (scegli, esegui, salva)
│   ├── Workouts.php          ← Crea / modifica / elimina workout
│   ├── Storico.php
│   ├── Peso.php
│   ├── Profilo.php
│   └── Admin/Utenti.php
├── Models/               ← parlano col database (una classe per tabella)
├── Libraries/
│   ├── Statistiche.php   ← calcoli PURI (niente database): settimane, mesi, %, 1RM
│   └── Dashboard.php     ← mette insieme i dati della Home
├── Helpers/
│   ├── gym_helper.php    ← formattare: kg(), durata(), data_breve(), quando()...
│   └── ui_helper.php     ← pezzi di HTML: icona(), trend(), sparkline(), errore(), componente()
├── Database/
│   ├── Migrations/       ← creano le tabelle (una per file)
│   └── Seeds/            ← riempiono le tabelle (stati, esercizi, utenti, demo)
├── Language/it/          ← messaggi di errore in italiano
└── Views/
    ├── layouts/app.php   ← la cornice: sidebar, barra in alto, menu mobile
    ├── partials/         ← pezzi riusabili (card, righe, stato vuoto, paginazione)
    ├── home/ allenamento/ workouts/ storico/ profilo/ admin/ auth/
public/
├── assets/css/           ← base, layout, componenti + un file per pagina
├── assets/js/            ← app.js + un file per pagina
└── assets/vendor/        ← Chart.js (copiato qui: funziona anche senza internet)
tests/unit/StatisticheTest.php
```

</details>

<details>
<summary>Il viaggio di una richiesta (es. apri la Home)</summary>

<br>

```
Browser: GET /
   │
   ▼
Routes.php ─── "/" → Home::index, dentro il gruppo con filtro "session"
   │
   ▼
Filtri "before": honeypot → csrf → session (sei loggato? no → vai al login)
   │
   ▼
Controllers/Home.php  index()
   │   new Dashboard($userId, 4)
   │      └── chiede ai Model: SingoloWorkoutModel, SerieEseguitaModel, PesoCorporeoModel...
   │      └── usa Statistiche per contare settimane/mesi e calcolare le %
   ▼
view('home/index', [...dati...])
   │   extend('layouts/app')  → la cornice
   │   componente('partials/stat_card', ...) → i pezzetti
   ▼
Filtri "after": honeypot (aggiunge il campo-trappola ai form), secureheaders
   │
   ▼
Browser: riceve l'HTML. home.js disegna i grafici con Chart.js
```

</details>

<details>
<summary>Controller: "sottili"</summary>

<br>

Un controller fa solo 3 cose:

1. **legge** l'input (`$this->request->getPost()`)
2. **valida** (`$this->validate($regole)`)
3. **chiama un Model** e **risponde** (`view()` o `redirect()`)

Le parti lunghe sono spezzate in **metodi privati con nomi parlanti**:
`serieDalForm()`, `dataEsecuzione()`, `workoutDelUtente()`, `righeDaMostrare()`…

<details>
<summary>Perché c'è un BaseController con userId() e nonTrovato()?</summary>

<br>

Perché li usano tutti. Invece di scrivere ogni volta `(int) auth()->id()`
o `throw PageNotFoundException::forPageNotFound()`, scrivi `$this->userId()` e `$this->nonTrovato()`.

</details>

</details>

<details>
<summary>Model: una classe per tabella</summary>

<br>

| Model | Tabella | Metodi principali |
|---|---|---|
| `WorkoutModel` | workouts | `trovaDelUtente()`, `conRiepilogo()`, `prossimi()`, `salvaConEsercizi()` |
| `WorkoutEsercizioModel` | workout_esercizi | `perWorkout()`, `riepilogoPerWorkout()`, `sostituisci()` |
| `EsercizioModel` | esercizi | `elenco()`, `trovaOCrea()` |
| `SingoloWorkoutModel` | singolo_workout | `ultimoPerWorkout()`, `ultimi()`, `dateTra()`, `salvaConSerie()` |
| `SerieEseguitaModel` | serie_eseguite | `perEsercizioESerie()`, `volumiTra()`, `miglioriPunteggiPrima()` |
| `PesoCorporeoModel` | peso_corporeo | `registra()`, `andamento()`, `ultime()` |
| `StatoModel` | stati | `elenco()`, `perId()` |

Regola: **ogni query sta in un Model**, con un nome che dice cosa trova.
Nei controller non c'è mai SQL.

<details>
<summary>Perché "trovaDelUtente()" e non semplicemente find()?</summary>

<br>

Sicurezza. `find(5)` trova il workout 5 di **chiunque**.
`trovaDelUtente(5, $userId)` lo trova solo se **è tuo**.
Così nessuno può vedere i dati degli altri cambiando il numero nell'URL: riceve un 404.

</details>

<details>
<summary>Cos'è una "transazione" (transStart / transComplete)?</summary>

<br>

Quando salvi un allenamento scrivi in **due** tabelle: `singolo_workout` (1 riga) e `serie_eseguite` (tante righe).
Se a metà qualcosa si rompe, senza transazione resterebbe un allenamento senza serie.
Con la transazione: **o si salva tutto, o niente**.

```php
$this->db->transStart();
// ... tutti gli insert ...
$this->db->transComplete();
```

</details>

</details>

<details>
<summary>Libraries: Statistiche e Dashboard</summary>

<br>

- **`Statistiche`**: funzioni **pure** (dai numeri, ricevi numeri; niente database).
  - `contaPerSettimana()`, `contaPerMese()`, `variazione()`, `massimaleStimato()`, `punteggioSerie()`
  - Essendo pure sono facili da testare → `tests/unit/StatisticheTest.php`
- **`Dashboard`**: prepara tutti i pezzi della Home (`cards()`, `frequenza()`, `progressi()`…).
  Così il controller Home è lungo 30 righe.

> In Laravel le chiameresti **Services**. In CodeIgniter la cartella "ufficiale" è `Libraries`.

<details>
<summary>Perché i conti per settimana/mese li faccio in PHP e non in SQL?</summary>

<br>

1. In SQL dovrei usare funzioni diverse per MySQL (`YEARWEEK`) e SQLite (`strftime`). In PHP funziona ovunque.
2. In PHP posso **testarlo** senza database.
3. Le settimane **vuote** (0 allenamenti) in SQL non esistono; in PHP le preparo io a zero, così il grafico non ha buchi.
4. Sono pochi dati (una persona fa ~200 allenamenti l'anno): nessun problema di velocità.

</details>

</details>

<details>
<summary>Helpers: piccole funzioni globali</summary>

<br>

Caricati in automatico ovunque (`app/Config/Autoload.php` → `$helpers`).

| Funzione | Esempio |
|---|---|
| `kg(82.50)` | `"82.5"` |
| `volume_breve(28400)` | `"28.4k"` |
| `con_segno(-0.8)` | `"−0.8"` |
| `durata(72)` | `"1h 12m"` |
| `data_breve('2026-10-20')` | `"20 Ott 2026"` |
| `quando('2026-10-20')` | `"3 giorni fa"` |
| `serie_testo(80, 6)` | `"80 kg × 6"` |
| `iniziale_workout('Push A')` | `"A"` |
| `icona('home')` | un `<svg>` |
| `trend(+3)` | la pillola verde `↑ +3` |
| `sparkline([...])` | mini grafico a linea in SVG |
| `errore('nome')` | il messaggio d'errore sotto un campo |
| `componente('partials/vuoto', [...])` | un pezzo di vista (vedi sotto) |

</details>

<details>
<summary>Views: layout, sezioni, componenti</summary>

<br>

- Ogni pagina fa `$this->extend('layouts/app')` → usa la cornice comune.
- Riempie i "buchi" della cornice con `$this->section('contenuto')` … `$this->endSection()`.
- Le sezioni `css` e `js` servono per caricare **solo** i file di quella pagina.

<details>
<summary>Perché componente() e non view() per i pezzetti?</summary>

<br>

Trappola di CodeIgniter: `view()` **ricorda i dati** tra una chiamata e l'altra.
Se al primo "stato vuoto" passi un `'link'` e al secondo no, il secondo mostra **lo stesso link** del primo!

`componente()` chiama `view()` con `saveData = false`: ogni pezzo vede **solo** i dati che gli passi.

</details>

</details>

<details>
<summary>CSS e JavaScript: niente build, niente framework</summary>

<br>

- **CSS puro** con variabili (`--accento`, `--superficie`…): cambi un colore in `base.css` e cambia ovunque.
- Nomi delle classi in stile **BEM**: `.blocco`, `.blocco__pezzo`, `.blocco--variante`.
- **JS vanilla** (niente jQuery, niente React). Un file per pagina, diviso in funzioni con nomi parlanti.
- Gli elementi che il JS usa hanno attributi `data-qualcosa` (es. `data-fatta`, `data-copia`): così puoi cambiare le classi CSS senza rompere il JS.
- Unica libreria: **Chart.js** per i due grafici grandi della Home. I mini grafici delle card sono SVG generati in PHP.

</details>

</details>

---

<details>
<summary><b>🗄️ Database</b></summary>

<br>

<details>
<summary>Lo schema (chi punta a chi)</summary>

<br>

```
users (Shield)
  │
  ├──< workouts ──────────────< workout_esercizi >──── esercizi
  │       (la scheda)               (cosa c'è dentro)     (catalogo)
  │                                                          │
  ├──< singolo_workout ──< serie_eseguite >──────────────────┘
  │       (un allenamento)    (ogni serie: peso, rep) ──> stati
  │
  └──< peso_corporeo
```

`──<` = "uno a molti". Es: un utente ha **molti** workout.

</details>

<details>
<summary>Le tabelle, una per una</summary>

<br>

<details>
<summary><code>workouts</code> — la scheda</summary>

<br>

| Colonna | Cosa |
|---|---|
| user_id | di chi è |
| nome | "Push A" |
| descrizione | facoltativa |
| colore | verde, blu, viola, arancio, rosa, ciano |
| deleted_at | pieno = eliminato (soft delete) |

</details>

<details>
<summary><code>esercizi</code> — il catalogo</summary>

<br>

`nome` (unico) + `gruppo_muscolare`. È **condiviso** tra gli utenti: così "Panca piana" è sempre lo stesso esercizio e i progressi si confrontano anche se lo fai in workout diversi.

</details>

<details>
<summary><code>workout_esercizi</code> — cosa c'è dentro una scheda</summary>

<br>

| Colonna | Esempio |
|---|---|
| workout_id | Push A |
| esercizio_id | Panca piana |
| ordine | 1 |
| serie | 4 |
| rep_target | "6-8" |
| recupero_secondi | 120 |
| note | "fermo al petto" |

</details>

<details>
<summary><code>singolo_workout</code> — un allenamento fatto</summary>

<br>

| Colonna | Cosa |
|---|---|
| user_id, workout_id | chi e quale scheda |
| data_esecuzione | quando |
| durata_minuti | dal cronometro |
| note | come è andata |

</details>

<details>
<summary><code>serie_eseguite</code> — ogni serie fatta</summary>

<br>

| Colonna | Esempio |
|---|---|
| singolo_workout_id | l'allenamento |
| esercizio_id | Panca piana |
| numero_serie | 2 |
| peso | 80.00 |
| rep | 6 |
| stato_id | Spottato (o vuoto) |

</details>

<details>
<summary><code>stati</code> e <code>peso_corporeo</code></summary>

<br>

- **stati:** nome, descrizione, colore, ordine. Riempita dal `StatiSeeder`.
- **peso_corporeo:** user_id, peso, data. **Una riga al giorno** per utente (chiave unica su `user_id + data`).

</details>

<details>
<summary>Le tabelle di Shield (non toccarle a mano)</summary>

<br>

| Tabella | Cosa contiene |
|---|---|
| users | username, stato (bloccato?), ultimo accesso |
| auth_identities | email + password criptata |
| auth_groups_users | il ruolo (admin / user) |
| auth_logins | i tentativi di login |
| auth_remember_tokens | il "ricordami" |
| auth_permissions_users, auth_token_logins | non usate da noi |
| settings | impostazioni salvate a db (pacchetto Settings) |

</details>

</details>

<details>
<summary>Perché l'allenamento è diviso in DUE tabelle?</summary>

<br>

Nella tua idea c'era una tabella "singolo allenamento con tracciamento di rep".
Ma un allenamento ha **tante** serie (20-25). Metterle tutte in una riga vorrebbe dire colonne tipo `rep_1, rep_2, … rep_30`: impossibile da interrogare.

Quindi:
- `singolo_workout` = **l'intestazione** (chi, cosa, quando) → 1 riga
- `serie_eseguite` = **il dettaglio** → 1 riga per serie

È la stessa cosa di "ordine" e "righe dell'ordine" in un e-commerce.

</details>

<details>
<summary>Come prendiamo i dati (le 3 regole che hai chiesto)</summary>

<br>

<details>
<summary>1. Ultime rep e serie fatte → <code>ultimoPerWorkout()</code></summary>

<br>

Da `singolo_workout`, per quel `workout_id`, prendo la riga con la `data_esecuzione` più recente. Poi le sue serie diventano i placeholder.

</details>

<details>
<summary>2. Frequenza → <code>dateTra()</code> + <code>Statistiche</code></summary>

<br>

Da `singolo_workout` prendo le date in un periodo, poi in PHP:
- `contaPerSettimana()` → andamento settimana per settimana
- `contaPerMese()` → per mese
- media mensile = allenamenti del periodo ÷ giorni × 30.4

</details>

<details>
<summary>3. Selezione workout → <code>WorkoutModel::prossimi()</code></summary>

<br>

Da `workouts` prendo le schede dell'utente con un riepilogo (n° esercizi, gruppi muscolari, ultima volta, durata media) e le ordino per "quale tocca adesso".

</details>

</details>

<details>
<summary>Foreign key e CASCADE: cosa si cancella da solo</summary>

<br>

| Se cancelli… | …spariscono anche |
|---|---|
| un utente | i suoi workout, allenamenti, serie, pesate |
| un allenamento | le sue serie |
| un workout (eliminazione vera, non soft) | i suoi esercizi-scheda e i suoi allenamenti |
| uno stato | niente: le serie restano, con stato vuoto (`SET NULL`) |

Un esercizio del catalogo **non** si può cancellare se è usato (`RESTRICT`).

</details>

</details>

---

<details>
<summary><b>🔐 Login, ruoli e anti-bot</b></summary>

<br>

<details>
<summary>Shield = Breeze + Spatie (in versione CodeIgniter)</summary>

<br>

**Shield** è la libreria di login **ufficiale** di CodeIgniter 4.

| Laravel | CodeIgniter Shield |
|---|---|
| Breeze (login/logout) | `service('auth')->routes($routes)` in Routes.php |
| `auth()->user()` | `auth()->user()` (uguale!) |
| `auth()->id()` | `auth()->id()` |
| middleware `auth` | filtro `session` |
| Spatie: ruoli | **gruppi** in `Config/AuthGroups.php` |
| `$user->hasRole('admin')` | `$user->inGroup('admin')` |
| `$user->can('...')` | `$user->can('utenti.gestisci')` |
| middleware `role:admin` | filtro `group:admin` |

Abbiamo **solo cambiato l'aspetto** del login (`Views/auth/login.php`). La logica è tutta di Shield.

</details>

<details>
<summary>I due ruoli</summary>

<br>

- **user** → usa l'app per i suoi allenamenti.
- **admin** → come user, più la pagina **Utenti** (permesso `utenti.gestisci`).

Nel codice: `is_admin()` (helper nostro) oppure `auth()->user()->inGroup('admin')`.

</details>

<details>
<summary>Le 4 protezioni anti-bot / anti-attacco</summary>

<br>

<details>
<summary>1. Honeypot (il "vasetto di miele")</summary>

<br>

In ogni form c'è un campo **nascosto** (`sito_web`). Un umano non lo vede e lo lascia vuoto.
Un bot compila tutto → il campo è pieno → richiesta bloccata.
È un filtro **già incluso in CodeIgniter**, acceso in `Config/Filters.php`.

</details>

<details>
<summary>2. Limite tentativi sul login (auth-rates)</summary>

<br>

Massimo **10 richieste al minuto per IP** sulla pagina di login. Un bot che prova mille password viene fermato.
È un filtro di Shield, acceso in `Config/Filters.php` → `$filters`.

</details>

<details>
<summary>3. CSRF</summary>

<br>

Ogni form ha un token segreto (`<?= csrf_field() ?>`). Un altro sito non può inviare form "a nome tuo".
Acceso per tutti i POST in `Config/Filters.php`.

</details>

<details>
<summary>4. Password forti + registrazione chiusa</summary>

<br>

- La regola `strong_password[]` di Shield rifiuta password corte, comuni (`password`, `12345678`…) o simili al nome/email.
- Registrazione pubblica **spenta**: gli utenti li crea l'admin.

</details>

</details>

</details>

---

<details>
<summary><b>🤔 Le scelte fatte (e le alternative scartate)</b></summary>

<br>

<details>
<summary>Design</summary>

<br>

- **Scuro + verde menta**, come la tua immagine, ma più pulito: niente foto negli elenchi.
- **Il "disco":** il badge del workout è disegnato come un **disco del bilanciere** (bordo + anello interno), con la lettera al centro. È il dettaglio "firma" dell'app.
- **Font:** *Archivo* largo per i titoli (un po' sportivo), *Geist* per il testo, *Geist Mono* per i numeri (kg, rep, tempi: si leggono meglio e restano in colonna).
- **Telefono prima di tutto** nella pagina Workout: campi alti 46px, testo 18px, ✓ grande, menu in basso.
- Sui telefoni i campi hanno testo ≥ 16px: sotto quella misura iPhone fa lo zoom quando li tocchi.

</details>

<details>
<summary>Codice</summary>

<br>

| Scelta | Perché | Alternativa scartata |
|---|---|---|
| Shield | è l'auth ufficiale, sicura, con ruoli | auth fatta a mano (più codice da mantenere e meno sicura) |
| CSS puro | niente build, si apre e si capisce | Tailwind (serve un build step) |
| JS vanilla | niente da installare | jQuery / Alpine / React |
| Calcoli in PHP | funziona con MySQL e SQLite, si testa | query SQL complesse |
| Chart.js in locale | funziona anche senza rete in palestra | CDN |
| Bozza in localStorage | non perdi l'allenamento se si ricarica la pagina | salvataggio automatico sul server (più complicato) |
| Soft delete sui workout | lo storico resta leggibile | cancellazione vera |
| Catalogo esercizi condiviso | progressi confrontabili tra workout | esercizi copiati in ogni scheda |

</details>

<details>
<summary>Sessione e CSRF a 4 ore</summary>

<br>

Di default CodeIgniter fa scadere sessione e token dopo 2 ore.
Un allenamento + qualche pausa può superarle → al salvataggio ti troveresti sloggato.
Li ho portati a **4 ore** (`Config/Session.php`, `Config/Security.php`).
E comunque, anche se scadesse, la **bozza** ti salva i dati.

</details>

</details>

---

<details>
<summary><b>📖 Dizionario CodeIgniter (tutte le parole usate)</b></summary>

<br>

<details>
<summary>Routing e Controller</summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `$routes->get('url', 'Classe::metodo')` | quando apri quell'URL, esegui quel metodo |
| `(:num)` | nell'URL accetto solo un numero; arriva al metodo come parametro (`$1`) |
| `$routes->group('admin', ['filter' => ...])` | tutte le rotte dentro hanno lo stesso prefisso e gli stessi filtri |
| `$this->request->getPost('nome')` | legge un campo del form (POST) |
| `$this->request->getGet('periodo')` | legge un parametro dell'URL (`?periodo=12`) |
| `$this->validate($regole)` | controlla i dati del form con le regole |
| `$this->validateData($dati, $regole)` | come sopra, ma su un array che prepari tu |
| `$this->validator->getErrors()` | gli errori trovati |
| `redirect()->to('url')` | manda il browser a un'altra pagina |
| `redirect()->back()` | torna alla pagina di prima |
| `->withInput()` | ricorda cosa avevi scritto nel form |
| `->with('successo', '...')` | messaggio "flash": dura una sola pagina |
| `session('successo')` | legge quel messaggio |

</details>

<details>
<summary>Model e database</summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `model(WorkoutModel::class)` | ti dà il model (sempre la stessa istanza, non ne crea mille) |
| `$table` | la tabella del model |
| `$allowedFields` | le colonne che puoi salvare (protezione: le altre vengono ignorate) |
| `$returnType = 'array'` | i risultati arrivano come array |
| `$useTimestamps = true` | riempie da solo `created_at` e `updated_at` |
| `$useSoftDeletes = true` | `delete()` non cancella: riempie `deleted_at` |
| `find($id)` / `first()` / `findAll()` | uno per id / il primo / tutti |
| `where()`, `whereIn()`, `orderBy()`, `join()` | Query Builder: costruisci la query con PHP |
| `selectMax()`, `selectAvg()`, `selectCount()` | MAX, AVG, COUNT in SQL |
| `insert()`, `update()`, `delete()` | crea, modifica, cancella |
| `paginate(12)` + `$model->pager` | 12 risultati per pagina + i link pagina |
| `transStart()` / `transComplete()` | transazione: tutto o niente |
| Migration | un file che **crea** o modifica una tabella (`php spark migrate`) |
| `$this->forge->addField()` | (nelle migration) definisce le colonne |
| `addForeignKey(..., 'CASCADE', 'CASCADE')` | collegamento tra tabelle + cosa fare quando si cancella |
| Seeder | un file che **riempie** le tabelle (`php spark db:seed NomeSeeder`) |

</details>

<details>
<summary>Viste</summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `view('home/index', $dati)` | mostra `app/Views/home/index.php` con quei dati |
| `$this->extend('layouts/app')` | questa pagina usa quella cornice |
| `$this->section('x')` … `$this->endSection()` | riempie il "buco" x della cornice |
| `$this->renderSection('x')` | (nella cornice) qui va il contenuto della sezione x |
| `$this->include('partials/sidebar')` | inserisce un'altra vista |
| `esc($testo)` | **sempre** prima di stampare testo dell'utente: blocca l'XSS |
| `old('nome', $default)` | il valore scritto prima dell'errore (o il default) |
| `csrf_field()` | il campo nascosto col token CSRF |
| `site_url('storico')` | URL completo di una pagina |
| `base_url('assets/css/x.css')` | URL completo di un file in `public/` |
| `url_is('storico*')` | l'URL attuale è questo? (per evidenziare il menu) |

</details>

<details>
<summary>Config, Helper, Library, Filtri</summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `config('Gym')` | legge `app/Config/Gym.php` |
| Helper | file di funzioni sciolte; si caricano con `helper('nome')` o in `Autoload.php` |
| Library | una classe "tua" in `app/Libraries` per la logica che non è né controller né model |
| Filtro | codice che gira **prima** o **dopo** il controller (login, CSRF, honeypot…) |
| `before` / `after` | prima del controller / dopo, sulla risposta |
| `.env` | configurazione del TUO computer (database, password): mai su GitHub |
| `php spark` | il terminale di CodeIgniter (come `php artisan`) |

</details>

<details>
<summary>Shield</summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `auth()->loggedIn()` | c'è qualcuno loggato? |
| `auth()->user()` | l'utente loggato (oggetto `User`) |
| `auth()->id()` | il suo id |
| `auth()->getProvider()` | il model degli utenti di Shield |
| `auth()->check([...])` | email+password giuste? (senza fare login) |
| `$user->inGroup('admin')` | ha quel ruolo? |
| `$user->addGroup()` / `syncGroups()` | aggiunge / sostituisce i ruoli |
| `$user->ban()` / `unBan()` / `isBanned()` | blocca / sblocca / è bloccato? |
| `$user->activate()` | attiva l'account |
| filtro `session` | serve il login |
| filtro `group:admin` | serve il ruolo admin |

</details>

</details>

---

<details>
<summary><b>🛠️ Ricette: come faccio a…</b></summary>

<br>

<details>
<summary>…aggiungere uno stato nuovo (es. "Dropset")?</summary>

<br>

1. Apri `app/Database/Seeds/StatiSeeder.php`
2. Aggiungi una riga nell'array `$stati`
3. `php spark db:seed StatiSeeder` (quelli già presenti non vengono duplicati)

Colori disponibili: `ambra`, `blu`, `viola`, `rosso`, `ciano`, `rosa`.

</details>

<details>
<summary>…aggiungere un gruppo muscolare o un colore?</summary>

<br>

`app/Config/Gym.php` → `$gruppiMuscolari` o `$coloriWorkout`.
Per un colore nuovo aggiungi anche la classe in `public/assets/css/componenti.css` (es. `.disco--giallo`).

</details>

<details>
<summary>…aggiungere una pagina nuova?</summary>

<br>

1. **Rotta** in `app/Config/Routes.php` (dentro il gruppo con `session`)
2. **Controller** in `app/Controllers/` che estende `BaseController`
3. **Vista** in `app/Views/…` che inizia con `<?= $this->extend('layouts/app') ?>`
4. (se serve) voce nel menu: `app/Views/partials/sidebar.php` e `nav_mobile.php`

</details>

<details>
<summary>…cambiare il colore principale?</summary>

<br>

`public/assets/css/base.css` → `--accento` (e `--accento-tenue`, `--accento-hover`).

</details>

<details>
<summary>…lanciare i test?</summary>

<br>

```bash
vendor/bin/phpunit
```

</details>

<details>
<summary>…riempire di nuovo i dati demo da zero?</summary>

<br>

```bash
php spark migrate:refresh --all   # ⚠️ CANCELLA TUTTO
php spark db:seed DatabaseSeeder
php spark db:seed DemoSeeder
```

</details>

<details>
<summary>…mettere l'app online?</summary>

<br>

- Nel `.env`: `CI_ENVIRONMENT = production` e `app.baseURL` con il tuo dominio.
- La cartella pubblica del server deve essere **`public/`** (non la cartella del progetto).
- Usa **HTTPS** (`app.forceGlobalSecureRequests = true` nel `.env`).

</details>

</details>

---

<details>
<summary><b>🧯 Problemi comuni</b></summary>

<br>

<details>
<summary>"Unable to connect to the database"</summary>

<br>

Controlla nel `.env`: `hostname`, `database`, `username`, `password`. Il database `gymlog` deve esistere già.

</details>

<details>
<summary>Dopo il login torno sempre al login</summary>

<br>

Probabilmente `app.baseURL` nel `.env` non corrisponde all'indirizzo che usi (es. `localhost` vs `127.0.0.1`). Devono essere uguali.

</details>

<details>
<summary>"Troppe richieste" sul login</summary>

<br>

È il limite anti-bot (10 al minuto). Aspetta un minuto.

</details>

<details>
<summary>"The action you requested is not allowed"</summary>

<br>

Token CSRF scaduto (pagina aperta da più di 4 ore). Ricarica la pagina; se eri nel Workout la bozza ti ridà i dati.

</details>

<details>
<summary>In basso a destra c'è un'icona con una fiamma</summary>

<br>

È la **Debug Toolbar** di CodeIgniter: compare solo con `CI_ENVIRONMENT = development`. In produzione sparisce.

</details>

</details>

---

<details>
<summary><b>💡 Idee per dopo (non fatte apposta, per restare semplici)</b></summary>

<br>

- Pagina admin per gestire gli **stati** e il **catalogo esercizi**
- Grafico dei progressi di **un singolo esercizio** nel tempo
- Esportare lo storico in CSV
- PWA completa (funzionare offline e salvare quando torna la rete)
- "Recupera password" via email (Shield lo supporta, serve configurare l'invio email)

</details>
