{{--
    LA CORNICE DI TUTTE LE PAGINE.
    Uso:  <x-layout title="Pesi"> ...contenuto... </x-layout>
    Slot opzionale "actions": bottoni a destra del titolo.
--}}
@props(['title' => 'GymLog', 'heading' => null, 'subtitle' => null])
@php
$nav = [
    ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Oggi', 'icon' => 'home'],
    ['route' => 'train.index', 'match' => 'train.*', 'label' => 'Allenamento', 'icon' => 'barbell'],
    ['route' => 'progress.index', 'match' => 'progress.*', 'label' => 'Pesi', 'icon' => 'chart'],
    ['route' => 'body.index', 'match' => 'body.*', 'label' => 'Corpo', 'icon' => 'body'],
    ['route' => 'coach.index', 'match' => 'coach.*', 'label' => 'Coach', 'icon' => 'coach'],
];
@endphp
<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#131514">
    <title>{{ $title }} · GymLog</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen pb-[calc(64px+env(safe-area-inset-bottom))] lg:pb-0">

    {{-- Barra in alto --}}
    <header class="bg-inchiostro text-gesso">
        <div class="max-w-6xl mx-auto px-4 lg:px-8 h-14 flex items-center gap-8">
            <a href="{{ route('dashboard') }}" class="titolo text-2xl tracking-wide">Gym<span class="text-disco-rosso">/</span>Log</a>

            <nav class="hidden lg:flex items-stretch self-stretch gap-1" aria-label="Menu principale">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" @class([
                        'flex items-center px-3 text-sm border-b-2 transition-colors',
                        'border-disco-rosso text-white' => request()->routeIs($item['match']),
                        'border-transparent text-gesso/60 hover:text-white' => ! request()->routeIs($item['match']),
                    ])>{{ $item['label'] }}</a>
                @endforeach
            </nav>

            {{-- Menu utente: <details> si apre e chiude da solo, niente JS --}}
            <details class="ml-auto relative group" data-menu>
                <summary class="list-none cursor-pointer flex items-center gap-2 text-sm text-gesso/80 hover:text-white [&::-webkit-details-marker]:hidden">
                    <span class="grid place-items-center w-8 h-8 rounded-sm bg-white/10 font-semibold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                    <x-icon name="chevron-down" size="14" />
                </summary>
                <div class="absolute right-0 top-11 z-30 w-52 pannello text-inchiostro py-1 text-sm">
                    <a href="{{ route('history.index') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-cemento"><x-icon name="history" size="16" /> Storico</a>
                    <a href="{{ route('workouts.create') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-cemento"><x-icon name="plus" size="16" /> Nuova scheda</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-cemento"><x-icon name="user" size="16" /> Profilo</a>
                    @role('admin')
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 hover:bg-cemento"><x-icon name="users" size="16" /> Utenti</a>
                    @endrole
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-riga mt-1 pt-1">
                        @csrf
                        <button class="w-full flex items-center gap-2 px-3 py-2 hover:bg-cemento cursor-pointer"><x-icon name="logout" size="16" /> Esci</button>
                    </form>
                </div>
            </details>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 lg:px-8 py-6 lg:py-8">
        @if ($heading)
            <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
                <div class="min-w-0">
                    <h1 class="titolo text-5xl sm:text-6xl">{{ $heading }}</h1>
                    @if ($subtitle)<p class="text-grafite mt-2">{{ $subtitle }}</p>@endif
                </div>
                @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 flex items-center gap-2 border-l-4 border-disco-verde bg-gesso px-4 py-3 text-sm" role="status" data-flash>
                <x-icon name="check" size="16" class="text-disco-verde" /> {{ session('success') }}
            </div>
        @endif
        @if (session('clear_draft'))
            <span hidden data-clear-draft="{{ session('clear_draft') }}"></span>
        @endif

        {{ $slot }}
    </main>

    {{-- Menu in basso (telefono) --}}
    <nav class="lg:hidden fixed inset-x-0 bottom-0 z-20 bg-inchiostro text-gesso grid grid-cols-5 h-[calc(64px+env(safe-area-inset-bottom))] pb-[env(safe-area-inset-bottom)]" aria-label="Menu">
        @foreach ($nav as $item)
            <a href="{{ route($item['route']) }}" @class([
                'flex flex-col items-center justify-center gap-1 text-[11px] border-t-2',
                'border-disco-rosso text-white' => request()->routeIs($item['match']),
                'border-transparent text-gesso/55' => ! request()->routeIs($item['match']),
            ])><x-icon :name="$item['icon']" size="21" />{{ $item['label'] }}</a>
        @endforeach
    </nav>
</body>
</html>
