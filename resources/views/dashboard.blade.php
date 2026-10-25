<x-layout title="Oggi" :heading="'Ciao, '.auth()->user()->name.'.'" :subtitle="$data['next']->first() ? 'Oggi tocca a: '.$data['next']->first()->name : 'Crea la tua prima scheda per iniziare.'">
    <x-slot:actions>
        {{-- filtro periodo: form GET, cambia solo l'URL (?periodo=12) --}}
        <form method="GET">
            <label for="periodo" class="sr-only">Periodo</label>
            <select id="periodo" name="periodo" class="input !h-10 min-w-52" data-autosubmit>
                @foreach ($periods as $value => $label)
                    <option value="{{ $value }}" @selected($value === $weeks)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </x-slot:actions>

    {{-- 4 numeri --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <x-stat label="Allenamenti" :value="$data['sessions']['value']">
            <x-trend :delta="$data['sessions']['delta']" /><span>vs periodo prima</span>
        </x-stat>

        <x-stat label="A settimana" :value="kg($data['per_week']['value'])">
            <x-trend :delta="$data['per_week']['delta']" /><span>≈ {{ kg($data['per_week']['per_month']) }} al mese</span>
        </x-stat>

        <x-stat label="Peso corporeo" :value="$data['body']['value'] ? kg($data['body']['value']) : '—'" :unit="$data['body']['value'] ? 'kg' : null">
            <x-trend :delta="$data['body']['delta']" unit=" kg" neutral /><x-spark :values="$data['body']['series']" />
        </x-stat>

        <x-stat label="Volume" :value="volume_breve($data['volume']['value'])" unit="kg">
            <x-trend :delta="$data['volume']['change']" unit="%" /><span>kg × rep</span>
        </x-stat>
    </div>

    <div class="grid lg:grid-cols-3 gap-3 mt-3">
        {{-- PESI: il pezzo più importante --}}
        <x-panel title="Come vanno i pesi" subtitle="Peso medio delle serie: inizio periodo → ultima volta" class="lg:col-span-2">
            <x-slot:aside><a href="{{ route('progress.index') }}" class="btn btn-muto btn-piccolo">Tutti <x-icon name="arrow-right" size="14" /></a></x-slot:aside>

            @forelse ($data['lifts'] as $lift)
                <a href="{{ route('progress.show', $lift['exercise_id']) }}" class="grid grid-cols-[1fr_auto_auto] sm:grid-cols-[1fr_96px_auto_auto] items-center gap-3 sm:gap-5 py-3 border-t border-riga first:border-t-0 hover:bg-cemento/60 -mx-2 px-2 rounded-sm">
                    <span class="min-w-0">
                        <span class="block font-medium truncate">{{ $lift['exercise'] }}</span>
                        <span class="block text-[12px] text-polvere">{{ $lift['sessions'] }} volte · prima {{ kg($lift['first_avg']) }} kg</span>
                    </span>
                    <x-spark :values="$lift['series']" class="hidden sm:block text-grafite" />
                    <span class="numero text-3xl text-right">{{ kg($lift['last_avg']) }}<span class="text-sm text-grafite font-sans font-normal"> kg</span></span>
                    <x-trend :delta="$lift['change']" unit="%" class="justify-self-end" />
                </a>
            @empty
                <x-empty title="Ancora niente da confrontare" text="Fai lo stesso esercizio almeno due volte nel periodo e qui vedi se il peso sale." :href="route('train.index')" cta="Allenati" />
            @endforelse
        </x-panel>

        {{-- PROSSIMO ALLENAMENTO --}}
        <x-panel title="Prossimo allenamento">
            @forelse ($data['next'] as $i => $workout)
                <a href="{{ route('train.show', $workout) }}" @class(['flex items-center gap-3 py-3 border-t border-riga first:border-t-0 group', 'pb-4' => $i === 0])>
                    <x-plate :color="$workout->color" :letter="$workout->initial()" :size="$i === 0 ? 'lg' : 'md'" />
                    <span class="min-w-0 flex-1">
                        <span @class(['block font-semibold truncate', 'text-lg' => $i === 0])>{{ $workout->name }}</span>
                        <span class="block text-[12px] text-polvere">{{ $workout->items_count }} esercizi · ~{{ $workout->avg_minutes }} min · {{ $workout->last_done_at ? \Carbon\Carbon::parse($workout->last_done_at)->diffForHumans() : 'mai fatto' }}</span>
                    </span>
                    <span @class(['grid place-items-center w-9 h-9 rounded-sm transition-colors', 'bg-disco-rosso text-white' => $i === 0, 'bg-cemento group-hover:bg-inchiostro group-hover:text-white' => $i > 0])><x-icon name="play" size="14" /></span>
                </a>
            @empty
                <x-empty title="Nessuna scheda" text="Una scheda è il tuo allenamento: esercizi, serie, rep e pesi." :href="route('workouts.create')" cta="Crea scheda" />
            @endforelse
        </x-panel>

        {{-- FREQUENZA --}}
        <x-panel title="Frequenza" subtitle="Allenamenti per settimana" class="lg:col-span-2" data-frequency>
            <x-slot:aside>
                <div class="flex border border-riga rounded-sm text-xs" role="group" aria-label="Raggruppa per">
                    <button type="button" class="px-3 h-8 bg-inchiostro text-white cursor-pointer" data-frequency-tab="weeks" aria-pressed="true">Settimane</button>
                    <button type="button" class="px-3 h-8 cursor-pointer" data-frequency-tab="months" aria-pressed="false">Mesi</button>
                </div>
            </x-slot:aside>
            <div class="h-56">
                <canvas data-chart="{{ json_encode(['type' => 'bars', 'weeks' => $data['frequency']['weeks'], 'months' => $data['frequency']['months']]) }}" role="img" aria-label="Allenamenti per settimana"></canvas>
            </div>
        </x-panel>

        {{-- ULTIMI ALLENAMENTI --}}
        <x-panel title="Ultimi allenamenti">
            <x-slot:aside><a href="{{ route('history.index') }}" class="btn btn-muto btn-piccolo">Storico <x-icon name="arrow-right" size="14" /></a></x-slot:aside>
            @forelse ($data['recent'] as $session)
                <a href="{{ route('history.show', $session) }}" class="flex items-center gap-3 py-2.5 border-t border-riga first:border-t-0 hover:bg-cemento/60 -mx-2 px-2 rounded-sm">
                    <x-plate :color="$session->workout->color" :letter="$session->workout->initial()" size="sm" />
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm font-medium truncate">{{ $session->workout->name }}</span>
                        <span class="block text-[12px] text-polvere">{{ $session->performed_at->translatedFormat('D j M') }} · {{ durata($session->duration_minutes) }}</span>
                    </span>
                    <span class="font-mono text-sm">{{ volume_breve($session->volume()) }}</span>
                </a>
            @empty
                <x-empty title="Nessun allenamento" text="Quando ne salvi uno, lo trovi qui." />
            @endforelse
        </x-panel>
    </div>
</x-layout>
