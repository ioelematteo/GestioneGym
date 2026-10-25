<x-layout title="Home">
    @php $next = $data['next']->first(); @endphp

    {{-- Saluto + periodo --}}
    <div class="mb-5 lg:mb-8">
        <div class="flex items-center justify-between gap-3">
            <p class="text-[14px] text-fg-3 first-letter:uppercase">{{ now()->translatedFormat('l j F') }}</p>
            {{-- form GET: cambia solo l'URL (?periodo=12) --}}
            <form method="GET" class="shrink-0">
                <label for="periodo" class="sr-only">Periodo</label>
                <select id="periodo" name="periodo" class="input !h-9 !rounded-full !text-[13px] !pl-3.5 !pr-9 !bg-surface w-auto text-fg-2" data-autosubmit>
                    @foreach ($periods as $value => $label)
                        <option value="{{ $value }}" @selected($value === $weeks)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <h1 class="title text-[32px] lg:text-[44px] mt-1 truncate">Ciao, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</h1>
    </div>

    {{-- Prossimo allenamento: l'azione più frequente, in cima --}}
    @if ($next)
        <a href="{{ route('train.show', $next) }}" class="card flex items-center gap-4 p-4 sm:p-5 mb-3 hover:border-line-strong transition-colors">
            <x-badge :color="$next->color" :letter="$next->initial()" size="lg" />
            <span class="min-w-0 flex-1">
                <span class="block text-[13px] text-fg-3">Oggi tocca a</span>
                <span class="block title text-[22px] truncate">{{ $next->name }}</span>
                <span class="block text-[13px] text-fg-3 truncate">{{ $next->items_count }} esercizi · ~{{ $next->avg_minutes }} min</span>
            </span>
            <span class="btn btn-primary shrink-0 !px-5"><x-icon name="play" size="14" /><span class="hidden sm:inline">Inizia</span></span>
        </a>
    @else
        <x-panel class="mb-3">
            <x-empty title="Crea la tua prima scheda" text="Una scheda contiene gli esercizi con serie, rep e peso." :href="route('workouts.create')" cta="Nuova scheda" />
        </x-panel>
    @endif

    {{-- 4 numeri --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
        <x-stat label="Allenamenti" :value="$data['sessions']['value']">
            <x-trend :delta="$data['sessions']['delta']" /><span class="hidden sm:inline">vs prima</span>
        </x-stat>
        <x-stat label="A settimana" :value="kg($data['per_week']['value'])">
            <x-trend :delta="$data['per_week']['delta']" /><span>{{ kg($data['per_week']['per_month']) }}/mese</span>
        </x-stat>
        <x-stat label="Peso corporeo" :value="$data['body']['value'] ? kg($data['body']['value']) : '—'" :unit="$data['body']['value'] ? 'kg' : null">
            <x-trend :delta="$data['body']['delta']" unit=" kg" neutral /><x-spark :values="$data['body']['series']" width="56" height="20" />
        </x-stat>
        <x-stat label="Volume" :value="volume_breve($data['volume']['value'])" unit="kg">
            <x-trend :delta="$data['volume']['change']" unit="%" /><span class="hidden sm:inline">kg × rep</span>
        </x-stat>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
        {{-- COME VANNO I PESI --}}
        <x-panel title="Come vanno i pesi" subtitle="Peso medio delle serie, ultima volta" class="lg:col-span-2">
            <x-slot:aside><a href="{{ route('progress.index') }}" class="btn btn-ghost btn-sm -mr-2">Tutti <x-icon name="arrow-right" size="14" /></a></x-slot:aside>
            <div class="rows -mx-4 sm:-mx-5">
                @forelse ($data['lifts'] as $lift)
                    <a href="{{ route('progress.show', $lift['exercise_id']) }}" class="flex items-center gap-3 px-4 sm:px-5 py-3 hover:bg-surface-2/60 transition-colors">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] truncate" style="font-weight: 560">{{ $lift['exercise'] }}</span>
                            <span class="block text-[12px] text-fg-3">{{ $lift['sessions'] }} volte · prima {{ kg($lift['first_avg']) }} kg</span>
                        </span>
                        <x-spark :values="$lift['series']" width="72" height="24" class="hidden sm:block" />
                        <span class="flex flex-col items-end gap-1 sm:flex-row sm:items-center sm:gap-3">
                            <span class="num text-[20px] leading-none">{{ kg($lift['last_avg']) }}<span class="text-[12px] text-fg-3 font-normal"> kg</span></span>
                            <x-trend :delta="$lift['change']" unit="%" />
                        </span>
                    </a>
                @empty
                    <div class="px-4 sm:px-5"><x-empty title="Ancora niente da confrontare" text="Fai lo stesso esercizio almeno due volte nel periodo." /></div>
                @endforelse
            </div>
        </x-panel>

        {{-- FREQUENZA --}}
        <x-panel title="Frequenza" subtitle="Allenamenti per settimana" data-frequency>
            <x-slot:aside>
                <div class="flex p-0.5 rounded-full bg-surface-2 border border-line text-[12px]" role="group" aria-label="Raggruppa per">
                    <button type="button" class="h-7 px-3 rounded-full text-fg-3 aria-pressed:bg-surface-3 aria-pressed:text-fg cursor-pointer" data-frequency-tab="weeks" aria-pressed="true">Sett.</button>
                    <button type="button" class="h-7 px-3 rounded-full text-fg-3 aria-pressed:bg-surface-3 aria-pressed:text-fg cursor-pointer" data-frequency-tab="months" aria-pressed="false">Mesi</button>
                </div>
            </x-slot:aside>
            <div class="h-44 lg:h-52">
                <canvas data-chart="{{ json_encode(['type' => 'bars', 'weeks' => $data['frequency']['weeks'], 'months' => $data['frequency']['months']]) }}" role="img" aria-label="Allenamenti per settimana"></canvas>
            </div>
        </x-panel>

        {{-- PESO CORPOREO --}}
        <x-panel title="Peso corporeo" subtitle="Ultimi 3 mesi">
            <x-slot:aside><a href="{{ route('body.index') }}" class="btn btn-ghost btn-sm -mr-2">Corpo <x-icon name="arrow-right" size="14" /></a></x-slot:aside>
            @if (count($data['body_chart']) >= 2)
                <div class="h-44"><canvas data-chart="{{ json_encode(['type' => 'line', 'unit' => 'kg', 'points' => $data['body_chart']]) }}" role="img" aria-label="Andamento del peso corporeo"></canvas></div>
            @else
                <x-empty title="Servono due pesate" text="Segna il peso dalla pagina Corpo." :href="route('body.index')" cta="Pesati" />
            @endif
        </x-panel>

        {{-- ULTIMI ALLENAMENTI --}}
        <x-panel title="Ultimi allenamenti">
            <x-slot:aside><a href="{{ route('history.index') }}" class="btn btn-ghost btn-sm -mr-2">Storico <x-icon name="arrow-right" size="14" /></a></x-slot:aside>
            <div class="rows -mx-4 sm:-mx-5">
                @forelse ($data['recent'] as $session)
                    <a href="{{ route('history.show', $session) }}" class="flex items-center gap-3 px-4 sm:px-5 py-3 hover:bg-surface-2/60 transition-colors">
                        <x-badge :color="$session->workout->color" :letter="$session->workout->initial()" size="sm" />
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] truncate" style="font-weight: 560">{{ $session->workout->name }}</span>
                            <span class="block text-[12px] text-fg-3">{{ $session->performed_at->translatedFormat('D j M') }} · {{ durata($session->duration_minutes) }}</span>
                        </span>
                        <span class="num text-[15px] text-fg-2">{{ volume_breve($session->volume()) }}<span class="text-[11px] text-fg-3 font-normal"> kg</span></span>
                    </a>
                @empty
                    <div class="px-4 sm:px-5"><x-empty title="Nessun allenamento" text="Quando ne salvi uno, lo trovi qui." /></div>
                @endforelse
            </div>
        </x-panel>

        {{-- ALTRE SCHEDE --}}
        <x-panel title="Schede">
            <x-slot:aside><a href="{{ route('workouts.create') }}" class="btn btn-ghost btn-sm btn-icon -mr-2" aria-label="Nuova scheda"><x-icon name="plus" size="18" /></a></x-slot:aside>
            <div class="rows -mx-4 sm:-mx-5">
                @forelse ($data['next'] as $workout)
                    <a href="{{ route('train.show', $workout) }}" class="flex items-center gap-3 px-4 sm:px-5 py-3 hover:bg-surface-2/60 transition-colors">
                        <x-badge :color="$workout->color" :letter="$workout->initial()" size="sm" />
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] truncate" style="font-weight: 560">{{ $workout->name }}</span>
                            <span class="block text-[12px] text-fg-3">{{ $workout->last_done_at ? \Carbon\Carbon::parse($workout->last_done_at)->diffForHumans() : 'mai fatta' }}</span>
                        </span>
                        <x-icon name="play" size="12" class="text-fg-3" />
                    </a>
                @empty
                    <div class="px-4 sm:px-5"><x-empty title="Nessuna scheda" :href="route('workouts.create')" cta="Crea" /></div>
                @endforelse
            </div>
        </x-panel>
    </div>
</x-layout>
