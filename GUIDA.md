# 🏋️ GymLog — la guida

> **In una riga:** un diario di palestra in Laravel, fatto a **scatole chiuse**. Ogni scatola fa una cosa sola,
> riceve dall'esterno quello che le serve (dependency injection) e la puoi cambiare senza toccare le altre.
>
> **Come leggere:** ogni ▸ è una scheda chiusa. Apri solo quella che ti serve adesso.

---

<details>
<summary><b>⚡ Avvio rapido</b></summary>

<br>

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan db:seed --class=DemoSeeder    # facoltativo: dati finti
npm install && npm run build
php artisan serve
```

Apri http://localhost:8000 · `ioelematteo@gmail.com` / `Matteo2026!` (admin) · `user@test.it` / `User2026!`

<details>
<summary>Cosa fa ogni seeder?</summary>

<br>

| Seeder | Cosa crea |
|---|---|
| `RoleSeeder` | i ruoli Spatie `admin` e `user` |
| `SetStateSeeder` | gli stati delle serie (Ultime kippate, Ultima mezza rep, Spottato, Cedimento, Rest-pause, Fastidio) |
| `UserSeeder` | i 2 utenti |
| `WorkoutPlanSeeder` | le schede **Allenamento A** e **B** di Matteo, con i pesi |
| `DemoSeeder` | (a parte) 10 settimane di allenamenti A/B e pesate finte |

`php artisan db:seed` lancia i primi quattro (`DatabaseSeeder`).

</details>

<details>
<summary>Le schede A e B (WorkoutPlanSeeder)</summary>

<br>

| Allenamento A | kg | Allenamento B | kg |
|---|---|---|---|
| Dip | corpo libero | Squat | 10 |
| Panca inclinata | 16 | Schiena | 15 |
| Chest press | 30 | Lat machine | 40 |
| Alzate laterali | 10 | Pulley | 35 |
| Shoulder press | 25 | Leg extension | 20 |
| French press | 20 | Leg curl | 20 |
| | | Curl | 7.5 |
| | | Curl hammer | 10 |

Serie, rep e recupero li ho scelti io: li cambi dalla pagina della scheda o in `database/seeders/WorkoutPlanSeeder.php`.

</details>

</details>

---

<details>
<summary><b>🗺️ Le pagine</b></summary>

<br>

| Pagina | URL | A cosa serve |
|---|---|---|
| Oggi | `/` | riepilogo: numeri, **come vanno i pesi**, prossima scheda, frequenza |
| Allenamento | `/allenamento` → `/allenamento/{scheda}` | segni le serie in palestra |
| Pesi | `/pesi` → `/pesi/{esercizio}` | il tracker dei pesi |
| Corpo | `/corpo` | peso, % grasso, vita, foto |
| Coach | `/coach` | chiedi all'IA |
| Schede | `/schede/nuova`, `/schede/{id}/modifica` | crea e modifica le schede |
| Storico | `/storico` | tutti gli allenamenti, con i record |
| Utenti | `/admin/utenti` | solo admin |

<details>
<summary><b>Allenamento: come funziona in palestra</b></summary>

<br>

- Ogni serie ha **il suo peso**: puoi fare 16, 16, 14 kg.
- Dentro i campi vedi in grigio i numeri dell'**ultima volta** (i "numeri fantasma"). Se è la prima volta, vedi il peso previsto dalla scheda.
- **✓** = serie fatta. Se i campi sono vuoti copia l'ultima volta e fa partire il **timer di recupero** (vibra alla fine).
- **+ Serie** aggiunge una riga.
- **Bozza:** tutto viene salvato nel telefono mentre scrivi. Se la pagina si ricarica, ritrovi tutto (anche il cronometro).
- Si salvano solo le serie con le rep scritte. Dopo il salvataggio, quell'allenamento diventa "l'ultimo".

<details>
<summary>Da dove arrivano i numeri fantasma? (codice)</summary>

<br>

`App\Services\Training\LastSessionLookup::forWorkout()` prende l'allenamento più recente di quella scheda:

```php
return $user->trainingSessions()
    ->where('workout_id', $workout->id)
    ->latest('performed_at')   // il più recente
    ->latest('id')             // a parità, l'ultimo salvato
    ->first();
```

Poi `setsByExercise()` le organizza come `[esercizio][numero serie] => serie`.

</details>

</details>

<details>
<summary><b>Pesi: il tracker</b></summary>

<br>

Per ogni esercizio e ogni allenamento `WeightProgress` calcola:

- **peso medio** delle serie (Squat 10, 10, 9, 9 → 9.5 kg) ← il numero principale
- **peso massimo**
- **massimale stimato** (Epley: peso × (1 + rep/30)) per capire qual è la serie migliore
- **volume** (kg × rep)

La pagina `/pesi` mostra il peso medio dell'ultima volta, la variazione % dalla prima volta e un mini grafico.
Il dettaglio mostra il grafico (linea = medio, punti rossi = massimo) e la tabella.

</details>

<details>
<summary><b>Corpo: misure e foto</b></summary>

<br>

- **Una misurazione al giorno**: se ti pesi di nuovo oggi, sostituisce quella di prima.
- Peso obbligatorio, % grasso e vita facoltativi.
- **Foto private**: non sono mai pubbliche. Passano da `PhotoController@show`, che controlla che siano tue.
- In locale stanno in `storage/app/private/progress-photos/{id utente}`; su Laravel Cloud nel bucket.

</details>

<details>
<summary><b>Coach IA (DeepSeek)</b></summary>

<br>

1. Scrivi una domanda (o lasciala vuota per un'analisi generale).
2. `CoachContext` raccoglie i tuoi dati delle ultime 6 settimane: schede, **ogni serie** con peso, rep e stato, il trend dei pesi, le misure, l'elenco delle foto.
3. `SystemPrompt` legge il prompt da `resources/prompts/coach.md`.
4. `DeepSeekClient` manda tutto a DeepSeek; la risposta viene salvata (`coach_reports`) e la rileggi quando vuoi.

> ⚠️ **Le foto:** i modelli di DeepSeek leggono solo testo. All'IA arrivano data, posa e note delle foto, non le immagini.
> Se un giorno vuoi l'analisi delle immagini, scrivi un nuovo `AiClient` per un modello che vede le immagini (vedi "Cambiare un pezzo").

<details>
<summary>Il system prompt: cosa dice</summary>

<br>

È in `resources/prompts/coach.md`. In breve:

- chi è: il tuo coach, parla italiano, diretto
- cosa riceve: i tuoi dati (e che le foto non le vede)
- come ragiona: prima i numeri, cerca schemi (esercizi fermi, cedimenti, fastidi, peso corporeo che cambia troppo in fretta)
- come risponde: sempre con **Cosa noto / Cosa sta andando bene / Cosa cambierei / Prossimo allenamento**
- regole: niente diagnosi mediche, niente dati inventati, max ~350 parole

Lo modifichi lì, senza toccare il codice.

</details>

<details>
<summary>Costi e limiti</summary>

<br>

- Massimo **10 domande all'ora** per utente (`throttle:10,60` sulla rotta).
- Senza `DEEPSEEK_API_KEY` la pagina funziona e ti dice cosa manca.

</details>

</details>

</details>

---

<details>
<summary><b>🧱 Architettura: le scatole</b></summary>

<br>

**L'idea:** il controller non calcola niente. Chiede a dei **servizi** (le scatole) e passa il risultato alla vista.
Ogni servizio riceve quello che gli serve **nel costruttore** (dependency injection): Laravel glielo consegna da solo.

```
Richiesta → Rotta → Controller → Servizi → Model → Database
                         ↓
                       Vista (Blade + componenti)
```

<details>
<summary>La mappa delle scatole</summary>

<br>

```
app/
├── Contracts/                    ← le "prese" (interfacce): cosa serve, non come
│   ├── AiClient.php              ← "qualcosa che risponde a una domanda"
│   └── PhotoStorage.php          ← "qualcosa che salva le foto"
├── Services/
│   ├── Training/
│   │   ├── LastSessionLookup     ← l'ultimo allenamento di una scheda (numeri fantasma)
│   │   ├── SessionRecorder       ← salva allenamento + serie (transazione)
│   │   ├── WorkoutPlanner        ← crea / modifica una scheda
│   │   └── NextWorkoutPicker     ← quale scheda tocca adesso
│   ├── Progress/
│   │   ├── Formula               ← calcoli puri (%, 1RM, media)
│   │   ├── Frequency             ← allenamenti per settimana / mese
│   │   └── WeightProgress        ← il tracker dei pesi
│   ├── Body/
│   │   ├── BodyLog               ← peso e misure
│   │   └── DiskPhotoStorage      ← implementa PhotoStorage su un disco Laravel
│   ├── Coach/
│   │   ├── Coach                 ← unisce prompt + dati + IA
│   │   ├── CoachContext          ← i tuoi dati in testo
│   │   ├── SystemPrompt          ← legge resources/prompts/coach.md
│   │   ├── DeepSeekClient        ← implementa AiClient con DeepSeek
│   │   └── MissingKeyAiClient    ← implementa AiClient quando manca la chiave
│   └── Dashboard/DashboardData   ← mette insieme i pezzi per la pagina "Oggi"
├── Providers/GymServiceProvider  ← il "quadro elettrico": quale scatola va in quale presa
├── Http/Controllers/             ← sottili: leggono, chiamano un servizio, rispondono
├── Http/Requests/                ← la validazione di ogni form
└── Models/                       ← una classe per tabella
```

</details>

<details>
<summary>Dependency injection, spiegata con un esempio</summary>

<br>

Il `Coach` **non sa** che esiste DeepSeek. Chiede solo "un AiClient":

```php
class Coach
{
    public function __construct(
        private AiClient $ai,          // chi risponde
        private SystemPrompt $prompt,  // le istruzioni
        private CoachContext $context, // i tuoi dati
    ) {}
}
```

Chi decide quale AiClient? `GymServiceProvider`:

```php
$this->app->bind(AiClient::class, fn () => new DeepSeekClient(...));
```

Nei test lo sostituisco con una risposta finta (`Http::fake()`), e il Coach non se ne accorge.

</details>

<details>
<summary>Cambiare un pezzo (es. un'altra IA)</summary>

<br>

1. Crea `app/Services/Coach/OpenAiClient.php` che `implements AiClient` (un solo metodo: `chat()`).
2. In `GymServiceProvider` cambia la riga del `bind(AiClient::class, ...)`.
3. Fine. Coach, controller, viste e test restano uguali.

Stessa cosa per le foto: un'altra classe che `implements PhotoStorage`.

</details>

<details>
<summary>Componenti Blade (i pezzi della grafica)</summary>

<br>

In `resources/views/components/`. Li usi come tag HTML:

| Componente | Uso |
|---|---|
| `<x-layout>` | la cornice di ogni pagina (barra in alto, menu in basso su telefono) |
| `<x-panel title="...">` | un pannello con titolo |
| `<x-stat label="..." value="...">` | numero grande |
| `<x-plate color="rosso" letter="A">` | il disco della scheda |
| `<x-trend :delta="...">` | la pillola ↑ +3 |
| `<x-spark :values="...">` | mini grafico in SVG |
| `<x-field label="..." name="...">` | campo di un form con l'errore |
| `<x-empty>` | "non c'è ancora niente" |
| `<x-delete-button>` | elimina con conferma |
| `<x-icon name="...">` | icone |

</details>

<details>
<summary>JavaScript: un modulo per cosa</summary>

<br>

`resources/js/app.js` accende i moduli in `resources/js/modules/`. Ognuno parte solo se nella pagina c'è il suo elemento.

| Modulo | Cosa fa |
|---|---|
| `common.js` | conferme, filtro periodo, menu, pulizia bozza |
| `charts.js` | i grafici (Chart.js) da `<canvas data-chart>` |
| `session-logger.js` | cronometro, ✓, + serie, recupero, bozza |
| `plan-editor.js` | righe del form scheda |
| `coach.js` | suggerimenti e "sto pensando…" |

</details>

</details>

---

<details>
<summary><b>🗄️ Database</b></summary>

<br>

```
users ─┬─< workouts ──< workout_exercises >── exercises
       │      (scheda)    (esercizio + serie + rep + PESO previsto)
       │
       ├─< training_sessions ──< session_sets >── exercises
       │      (allenamento fatto)  (ogni serie: PESO + rep + stato) ──> set_states
       │
       ├─< body_entries      (peso, % grasso, vita: una al giorno)
       ├─< progress_photos   (data, posa, percorso del file)
       └─< coach_reports     (domanda, risposta, modello)
```

<details>
<summary>Dalla tua idea ai nomi delle tabelle</summary>

<br>

| Tu | Tabella | Perché questo nome |
|---|---|---|
| tabella workout | `workouts` + `workout_exercises` | la scheda e cosa c'è dentro |
| singolo_workout | `training_sessions` | un allenamento fatto |
| tracciamento rep/peso | `session_sets` | una riga **per ogni serie**, con il suo peso |
| stati | `set_states` | |
| peso corporeo | `body_entries` | contiene anche % grasso e vita |

Nomi in inglese perché Laravel "indovina" le tabelle dai nomi dei Model (Workout → workouts). Tutto il resto (UI, commenti) è in italiano.

</details>

<details>
<summary>Cosa si cancella da solo (cascade)</summary>

<br>

| Se cancelli… | …spariscono anche |
|---|---|
| un utente | tutto il suo (schede, allenamenti, serie, misure, foto, report) |
| un allenamento | le sue serie |
| una scheda | **niente**: è un *soft delete*, lo storico resta |
| uno stato | le serie restano, senza stato |

</details>

</details>

---

<details>
<summary><b>🔐 Login, ruoli, anti-bot</b></summary>

<br>

| Cosa | Come |
|---|---|
| Login | `LoginController` con `Auth::attempt` (Breeze non serve: login e logout sono 30 righe) |
| Ruoli | **Spatie Permission**: `admin`, `user`. Nel codice: `$user->hasRole('admin')`, nelle viste `@role('admin')`, nelle rotte `->middleware('role:admin')` |
| Registrazione | spenta: gli utenti li crea l'admin |
| Anti-bot | **honeypot** (campo nascosto `website` in `LoginRequest`), **5 tentativi al minuto** (`throttle:5,1`), CSRF su ogni form |
| Utente bloccato | `is_active = false`: non riesce a fare login |
| Dati degli altri | `ChecksOwnership::ensureOwns()`: se non è tuo → 404 |
| Password | `Password::min(8)->uncompromised()`: rifiuta quelle finite in furti di dati |

</details>

---

<details>
<summary><b>🎨 La grafica (e perché)</b></summary>

<br>

Il tema scuro con il verde menta era il look da "app fatta dall'IA". La nuova direzione viene dalla palestra vera:

- **Cemento chiaro** come sfondo, **inchiostro nero** per testo e barre.
- **I colori dei dischi olimpici** (rosso 25 · blu 20 · giallo 15 · verde 10 · bianco 5) come colore delle schede.
- **Il disco**: il badge della scheda è un disco visto di lato.
- **Big Shoulders Display** per titoli e numeri, come un tabellone; **IBM Plex Sans/Mono** per il testo e i dati.
- Angoli quasi vivi (3–5 px), niente ombre, niente gradienti.
- Il **rosso** solo dove conta: il prossimo allenamento, l'ultimo valore nei grafici, i record, "Salva allenamento".

I token stanno in `resources/css/app.css` (`@theme`): li usi come classi Tailwind (`bg-cemento`, `text-disco-rosso`, `font-tabellone`).
I font arrivano da npm (`@fontsource`), quindi sono dentro il progetto e funzionano anche senza CDN.

</details>

---

<details>
<summary><b>📖 Dizionario Laravel (le parole usate)</b></summary>

<br>

| Dicitura | Cosa significa |
|---|---|
| `Route::get('pesi', [ProgressController::class, 'index'])->name('progress.index')` | URL → metodo del controller, con un nome per `route('progress.index')` |
| `{workout}` nella rotta + `Workout $workout` nel metodo | *route model binding*: Laravel trova da solo il record per id |
| `->middleware('auth')` | serve il login |
| FormRequest (`WorkoutRequest`) | la validazione di un form in una classe; il controller riceve dati già validi |
| `prepareForValidation()` | pulisce i dati prima di validarli (es. `56,8` → `56.8`) |
| `$request->validated()` | solo i campi validati |
| `#[Fillable([...])]` | i campi che puoi salvare in blocco (protezione) |
| `casts()` | trasforma i valori (date → Carbon, decimali → float) |
| `hasMany` / `belongsTo` | relazioni tra tabelle |
| `withTrashed()` / `SoftDeletes` | soft delete: il record resta, con `deleted_at` |
| `DB::transaction(fn () => ...)` | tutto o niente |
| `factory()->for($user)->create()` | crea record finti collegati (seeder e test) |
| `$this->app->bind(Interfaccia::class, ...)` | dependency injection: quale classe concreta usare |
| `<x-panel>` | componente Blade (`resources/views/components/panel.blade.php`) |
| `@vite([...])` | carica CSS e JS compilati da Vite |
| `Http::fake()` / `Storage::fake()` | nei test: finta IA, finto disco |
| `throttle:5,1` | massimo 5 richieste in 1 minuto |

</details>

---

<details>
<summary><b>🛠️ Ricette</b></summary>

<br>

<details>
<summary>Aggiungere uno stato della serie</summary>

<br>Una riga in `database/seeders/SetStateSeeder.php`, poi `php artisan db:seed --class=SetStateSeeder`.

</details>

<details>
<summary>Cambiare cosa riceve l'IA</summary>

<br>`app/Services/Coach/CoachContext.php`: ogni sezione è un metodo (`plans`, `sessions`, `weightTrend`, `bodyData`, `photos`). Per mandare di più, aggiungi un metodo e mettilo in `build()`. Settimane di dati: `config/gymlog.php` → `coach.weeks_of_data`.

</details>

<details>
<summary>Cambiare il comportamento del coach</summary>

<br>Modifica `resources/prompts/coach.md`. Modello: `DEEPSEEK_MODEL` nel `.env` (es. `deepseek-reasoner`).

</details>

<details>
<summary>Aggiungere una pagina</summary>

<br>

1. Rotta in `routes/web.php` dentro `middleware('auth')`
2. Controller sottile: inietta il servizio che ti serve nel metodo
3. Vista che inizia con `<x-layout title="...">`
4. Voce di menu: `$nav` in `resources/views/components/layout.blade.php`

</details>

</details>

---

<details>
<summary><b>✅ Test</b></summary>

<br>

`php artisan test` — 18 test:

- **Login**: entra/esce, utente bloccato, honeypot, admin vs user
- **Allenamento**: peso diverso per ogni serie, niente serie = errore, numeri fantasma, scheda di un altro = 404, scheda con peso + doppioni, tracker pesi
- **Corpo**: una misurazione al giorno, foto salvate sul disco (finto) e private
- **Coach**: i dati e il system prompt arrivano a DeepSeek (finto) e la risposta viene salvata; senza chiave errore chiaro
- **Formule**: %, massimale, settimane vuote

</details>
