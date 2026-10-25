# GymLog

Diario di palestra in **Laravel 13**: segni ogni serie con il suo peso, vedi come salgono i pesi,
tieni traccia di peso corporeo e foto, e chiedi consigli a un coach IA (DeepSeek) che legge i tuoi dati.

## Cosa fa

- **Oggi** — allenamenti nel periodo, media a settimana e al mese, peso corporeo, volume, *come vanno i pesi*, prossima scheda, frequenza
- **Allenamento** — scegli la scheda e segni kg e rep di **ogni serie**. Sotto ogni serie vedi cosa hai fatto l'ultima volta. Cronometro, timer di recupero, stati della serie (kippate, mezza rep, spottato…), bozza salvata nel telefono
- **Pesi** — per ogni esercizio: peso medio delle serie allenamento dopo allenamento, massimo, grafico
- **Corpo** — peso, % grasso, vita, foto dei progressi (private)
- **Coach** — DeepSeek riceve schede, serie, trend e misure, e risponde con un'analisi
- **Schede**, **Storico** (con record personali), **Profilo**, **Utenti** (solo admin)

## Avvio in locale

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # oppure configura MySQL/Postgres nel .env
php artisan migrate
php artisan db:seed                   # ruoli, stati, 2 utenti, schede A e B
php artisan db:seed --class=DemoSeeder   # facoltativo: 10 settimane di dati finti
npm install && npm run build
php artisan serve
```

| Email | Password | Ruolo |
|---|---|---|
| `ioelematteo@gmail.com` | `Matteo2026!` | admin |
| `user@test.it` | `User2026!` | user |

Cambia le password dopo il primo accesso (menu in alto a destra → Profilo).

## Laravel Cloud

1. Collega il repository (branch `main`).
2. Aggiungi un **database** (MySQL o Postgres): le variabili `DB_*` le imposta Laravel Cloud.
3. Aggiungi un **bucket** (object storage) per le foto e metti `GYMLOG_PHOTOS_DISK` = nome del disco del bucket.
4. Variabili d'ambiente: `APP_LOCALE=it`, `APP_TIMEZONE=Europe/Rome`, `DEEPSEEK_API_KEY=...`.
5. Build: `composer install --no-dev` e `npm ci && npm run build`. Deploy: `php artisan migrate --force`.
6. Solo la prima volta, dalla console di Laravel Cloud: `php artisan db:seed --force`.

## Test

```bash
php artisan test
```

## Documentazione

Architettura, database, scelte e dizionario Laravel: **[GUIDA.md](GUIDA.md)**.
