<!doctype html>
<html lang="it">
<head>
    @include('partials.head', ['title' => 'Accedi'])
</head>
<body class="min-h-dvh flex flex-col px-5 pt-[calc(env(safe-area-inset-top)+24px)] pb-[calc(env(safe-area-inset-bottom)+24px)]">
    <a href="{{ route('login') }}" class="flex items-center gap-2 sm:absolute sm:top-8 sm:left-10">
        <x-icon name="barbell" size="24" class="text-accent" />
        <span class="title text-[19px]">GymLog</span>
    </a>

    <main class="w-full max-w-sm m-auto pt-10 sm:pt-0">
        <h1 class="title text-[36px]">Bentornato.</h1>
        <p class="text-fg-3 mt-2 mb-8">Entra e segna il prossimo allenamento.</p>

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
            @csrf
            {{-- HONEYPOT: nascosto agli umani, i bot lo compilano e vengono bloccati --}}
            <div class="hidden" aria-hidden="true">
                <label for="website">Sito web</label>
                <input id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <x-field label="Email" name="email">
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" autocomplete="email" inputmode="email" required autofocus>
            </x-field>

            <x-field label="Password" name="password">
                <input id="password" name="password" type="password" class="input" autocomplete="current-password" required>
            </x-field>

            <label class="flex items-center gap-3 text-[14px] text-fg-2 py-1">
                <input type="checkbox" name="remember" class="size-5 rounded accent-[var(--color-accent)]" checked> Resta collegato su questo telefono
            </label>

            @error('website')<p class="error">{{ $message }}</p>@enderror

            <button class="btn btn-primary btn-lg w-full mt-2">Entra</button>
        </form>

        <p class="text-[13px] text-fg-3 mt-8">Non hai un account? Lo crea l'admin.</p>
    </main>
</body>
</html>
