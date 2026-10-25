<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#131514">
    <title>Accedi · GymLog</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen grid lg:grid-cols-[1.1fr_1fr]">

    {{-- Lato sinistro: il "tabellone" (solo schermi larghi) --}}
    <aside class="hidden lg:flex flex-col justify-between bg-inchiostro text-gesso p-12">
        <span class="titolo text-3xl">Gym<span class="text-disco-rosso">/</span>Log</span>
        <div>
            <p class="etichetta !text-gesso/50 mb-3">Panca inclinata · serie 3</p>
            <p class="numero text-[9rem]">16<span class="text-5xl text-gesso/50"> kg</span> <span class="text-disco-rosso">×</span> 9</p>
        </div>
        <div class="flex gap-1.5" aria-hidden="true">
            <x-plate color="rosso" size="lg" /><x-plate color="blu" size="lg" /><x-plate color="giallo" size="lg" /><x-plate color="verde" size="lg" /><x-plate color="bianco" size="lg" />
        </div>
    </aside>

    <main class="flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm">
            <span class="lg:hidden titolo text-3xl block mb-8">Gym<span class="text-disco-rosso">/</span>Log</span>
            <h1 class="titolo text-6xl">Accedi</h1>
            <p class="text-grafite mt-2 mb-8">Segna le serie, guarda i pesi salire.</p>

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf
                {{-- HONEYPOT: nascosto agli umani, i bot lo compilano --}}
                <div class="hidden" aria-hidden="true">
                    <label for="website">Sito web</label>
                    <input id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <x-field label="Email" name="email">
                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </x-field>

                <x-field label="Password" name="password">
                    <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
                </x-field>

                <label class="flex items-center gap-2 text-sm text-grafite">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-inchiostro"> Ricordami su questo dispositivo
                </label>

                @error('website')<p class="errore">{{ $message }}</p>@enderror

                <button class="btn btn-pieno h-12 text-base mt-2">Entra</button>
            </form>

            <p class="text-[13px] text-polvere mt-8">Non hai un account? Lo crea l'admin.</p>
        </div>
    </main>
</body>
</html>
