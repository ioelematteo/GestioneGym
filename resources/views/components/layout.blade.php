{{--
    LA CORNICE DI TUTTE LE PAGINE
    Telefono: barra in alto + menu in basso.  Desktop: barra laterale.

    <x-layout title="Pesi" heading="Pesi" subtitle="...">  ... </x-layout>
    - slot "actions": bottoni accanto al titolo
    - focus = true: "modalità palestra" (niente menu in basso: la pagina ha la sua barra)
--}}
@props(['title' => 'GymLog', 'heading' => null, 'subtitle' => null, 'focus' => false])
@php
$nav = [
    ['route' => 'dashboard', 'match' => 'dashboard', 'label' => 'Home', 'icon' => 'home'],
    ['route' => 'train.index', 'match' => ['train.*', 'workouts.*'], 'label' => 'Allena', 'icon' => 'barbell'],
    ['route' => 'progress.index', 'match' => 'progress.*', 'label' => 'Pesi', 'icon' => 'chart'],
    ['route' => 'body.index', 'match' => 'body.*', 'label' => 'Corpo', 'icon' => 'body'],
    ['route' => 'coach.index', 'match' => 'coach.*', 'label' => 'Coach', 'icon' => 'coach'],
];
$more = [
    ['route' => 'history.index', 'match' => 'history.*', 'label' => 'Storico', 'icon' => 'history'],
    ['route' => 'profile.edit', 'match' => 'profile.*', 'label' => 'Profilo', 'icon' => 'user'],
];
if (auth()->user()->isAdmin()) {
    $more[] = ['route' => 'admin.users.index', 'match' => 'admin.*', 'label' => 'Utenti', 'icon' => 'users'];
}
$user = auth()->user();
@endphp
<!doctype html>
<html lang="it">
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-dvh">
<div class="lg:grid lg:grid-cols-[248px_1fr] min-h-dvh">

    {{-- ===== Barra laterale (desktop) ===== --}}
    <aside class="hidden lg:flex flex-col sticky top-0 h-dvh border-r border-line px-4 py-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 mb-8">
            <x-icon name="barbell" size="24" class="text-accent" />
            <span class="title text-[20px]">GymLog</span>
        </a>

        <nav class="flex flex-col gap-1" aria-label="Menu principale">
            @foreach (array_merge($nav, [null], $more) as $item)
                @if ($item === null)
                    <div class="h-px bg-line my-3 mx-3"></div>
                    @continue
                @endif
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}" @class([
                    'flex items-center gap-3 h-11 px-3 rounded-xl text-[15px] transition-colors',
                    'bg-accent/10 text-accent' => $active,
                    'text-fg-2 hover:bg-surface hover:text-fg' => ! $active,
                ]) @if ($active) aria-current="page" @endif>
                    <x-icon :name="$item['icon']" size="20" /> {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="mt-auto flex items-center gap-3 px-3 pt-4 border-t border-line">
            <span class="grid place-items-center size-9 rounded-full bg-surface-2 text-accent num text-sm">{{ mb_substr($user->name, 0, 1) }}</span>
            <span class="min-w-0 flex-1 leading-tight">
                <span class="block text-[14px] truncate" style="font-weight: 560">{{ $user->name }}</span>
                <span class="block text-[12px] text-fg-3">{{ $user->isAdmin() ? 'Admin' : 'Atleta' }}</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-ghost btn-sm btn-icon" aria-label="Esci" title="Esci"><x-icon name="logout" size="18" /></button>
            </form>
        </div>
    </aside>

    <div class="min-w-0">
        {{-- ===== Barra in alto (telefono) ===== --}}
        @unless ($focus)
            <header class="lg:hidden sticky top-0 z-30 bg-bg/85 backdrop-blur-xl border-b border-line/60 pt-[env(safe-area-inset-top)]">
                <div class="flex items-center h-14 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-icon name="barbell" size="22" class="text-accent" />
                        <span class="title text-[18px]">GymLog</span>
                    </a>

                    {{-- <details> si apre e chiude da solo, niente JavaScript --}}
                    <details class="ml-auto relative" data-menu>
                        <summary class="list-none [&::-webkit-details-marker]:hidden grid place-items-center size-10 rounded-full bg-surface-2 text-accent num text-sm cursor-pointer">
                            {{ mb_substr($user->name, 0, 1) }}
                        </summary>
                        <div class="absolute right-0 top-12 w-56 card p-1.5 shadow-2xl shadow-black/60">
                            <p class="px-3 pt-2 pb-2.5 text-[13px] text-fg-3 border-b border-line mb-1">{{ $user->email }}</p>
                            @foreach ($more as $item)
                                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 h-11 px-3 rounded-xl text-[15px] text-fg-2 hover:bg-surface-2 hover:text-fg">
                                    <x-icon :name="$item['icon']" size="18" /> {{ $item['label'] }}
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full flex items-center gap-3 h-11 px-3 rounded-xl text-[15px] text-fg-2 hover:bg-surface-2 hover:text-fg cursor-pointer"><x-icon name="logout" size="18" /> Esci</button>
                            </form>
                        </div>
                    </details>
                </div>
            </header>
        @endunless

        <main @class([
            'max-w-[1180px] mx-auto px-4 lg:px-10 pt-5 lg:pt-10',
            'pb-[calc(88px+env(safe-area-inset-bottom))] lg:pb-12' => ! $focus,
            'pb-[calc(120px+env(safe-area-inset-bottom))] lg:pb-12' => $focus,
        ])>
            @if ($heading)
                <div class="flex flex-wrap items-end justify-between gap-x-4 gap-y-3 mb-5 lg:mb-8">
                    <div class="min-w-0">
                        <h1 class="title text-[30px] lg:text-[40px]">{{ $heading }}</h1>
                        @if ($subtitle)<p class="text-[15px] text-fg-3 mt-1.5">{{ $subtitle }}</p>@endif
                    </div>
                    @isset($actions)<div class="flex flex-wrap gap-2">{{ $actions }}</div>@endisset
                </div>
            @endif

            @if (session('success'))
                <div class="card mb-4 flex items-center gap-3 px-4 py-3 text-[14px]" role="status" data-flash>
                    <span class="grid place-items-center size-6 rounded-full bg-accent text-accent-ink"><x-icon name="check" size="14" /></span>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('clear_draft'))
                <span hidden data-clear-draft="{{ session('clear_draft') }}"></span>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

{{-- ===== Menu in basso (telefono) ===== --}}
@unless ($focus)
    <nav class="lg:hidden fixed inset-x-0 bottom-0 z-30 bg-bg/90 backdrop-blur-xl border-t border-line/70 pb-[env(safe-area-inset-bottom)]" aria-label="Menu">
        <div class="grid grid-cols-5 h-16">
            @foreach ($nav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}" @class([
                    'flex flex-col items-center justify-center gap-1 text-[11px] transition-colors',
                    'text-fg' => $active,
                    'text-fg-3' => ! $active,
                ]) @if ($active) aria-current="page" @endif style="font-weight: 540">
                    <span @class(['grid place-items-center h-7 w-12 rounded-full transition-colors', 'bg-accent/15 text-accent' => $active])>
                        <x-icon :name="$item['icon']" size="21" />
                    </span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </nav>
@endunless
</body>
</html>
