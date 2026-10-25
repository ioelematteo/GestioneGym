<x-layout :title="$workout->name">
    <a href="{{ route('train.index') }}" class="inline-flex items-center gap-1.5 text-sm text-grafite hover:text-inchiostro mb-4"><x-icon name="arrow-left" size="14" /> Schede</a>

    <div class="flex flex-wrap items-center gap-4 mb-6">
        <x-plate :color="$workout->color" :letter="$workout->initial()" size="lg" />
        <div class="flex-1 min-w-0">
            <h1 class="titolo text-4xl sm:text-6xl break-words">{{ $workout->name }}</h1>
            <p class="text-grafite text-sm mt-1">
                {{ $last ? 'Ultima volta '.$last->performed_at->diffForHumans().' · '.durata($last->duration_minutes) : 'Prima volta: oggi fissi il punto di partenza.' }}
            </p>
        </div>
        <div class="flex items-center gap-2 bg-inchiostro text-white px-4 h-12 rounded-sm w-full sm:w-auto justify-center" title="Da quanto ti stai allenando">
            <x-icon name="timer" size="18" class="text-disco-rosso" /><span class="numero text-3xl w-[4.5ch] text-right" data-clock>0:00</span>
        </div>
    </div>

    <div class="hidden mb-4 flex-wrap items-center gap-3 border-l-4 border-disco-giallo bg-gesso px-4 py-3 text-sm" data-draft-notice>
        <x-icon name="repeat" size="16" /> Ho ripreso l'allenamento che avevi lasciato a metà.
        <button type="button" class="btn btn-muto btn-piccolo ml-auto" data-draft-discard>Ricomincia da zero</button>
    </div>

    @if ($last?->notes)
        <p class="mb-4 flex gap-2 border-l-4 border-disco-giallo bg-gesso px-4 py-3 text-sm text-grafite"><x-icon name="note" size="16" class="mt-0.5" /> <span><b class="text-inchiostro">Nota dell'ultima volta:</b> {{ $last->notes }}</span></p>
    @endif

    @error('sets')
        <p class="mb-4 border-l-4 border-disco-rosso bg-gesso px-4 py-3 text-sm" role="alert">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('train.store', $workout) }}" class="grid lg:grid-cols-2 gap-3 items-start" data-session="{{ $workout->id }}">
        @csrf

        @foreach ($workout->items as $item)
            @php
            $previous = $lastSets[$item->exercise_id] ?? []; // le serie di quest'esercizio l'ultima volta
            $rows = max($item->sets, $previous ? max(array_keys($previous)) : 0);
            @endphp
            <section class="pannello p-4" data-exercise="{{ $item->exercise_id }}" data-rest="{{ $item->rest_seconds }}">
                <header class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-semibold text-lg leading-tight">{{ $item->exercise->name }}</h2>
                        <p class="font-mono text-[12px] text-grafite mt-1">{{ $item->sets }} × {{ $item->reps_target ?: '—' }} · rec {{ gmdate('i:s', $item->rest_seconds) }}</p>
                    </div>
                    @if ($item->weight_target !== null)
                        <span class="numero text-2xl shrink-0">{{ $item->weight_target > 0 ? kg($item->weight_target) : 'CL' }}<span class="text-xs font-sans font-normal text-grafite">{{ $item->weight_target > 0 ? ' kg' : '' }}</span></span>
                    @endif
                </header>
                @if ($item->notes)<p class="text-[13px] text-grafite mt-2">{{ $item->notes }}</p>@endif

                <div class="grid grid-cols-[22px_1fr_1fr_48px] gap-x-2 mt-3 etichetta" aria-hidden="true"><span></span><span class="text-center">kg</span><span class="text-center">rep</span></div>

                <div data-set-list>
                    @for ($n = 1; $n <= $rows; $n++)
                        @include('train._set', ['exerciseId' => $item->exercise_id, 'n' => $n, 'last' => $previous[$n] ?? null, 'target' => $item->weight_target])
                    @endfor
                </div>

                <button type="button" class="btn btn-muto btn-piccolo mt-2" data-add-set><x-icon name="plus" size="14" /> Serie</button>
            </section>
        @endforeach

        <section class="pannello p-4 lg:col-span-2 flex flex-col gap-4">
            <h2 class="font-semibold">Chiudi allenamento <span class="font-normal text-sm text-grafite">· si salvano le serie con le rep scritte</span></h2>
            <div class="grid sm:grid-cols-3 gap-3">
                <x-field label="Data" name="date">
                    <input id="date" type="date" name="date" class="input" value="{{ old('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                </x-field>
                <x-field label="Peso corporeo (facoltativo)" name="body_weight">
                    <input id="body_weight" name="body_weight" class="input font-mono" inputmode="decimal" placeholder="es. 58.4" value="{{ old('body_weight') }}">
                </x-field>
                <x-field label="Note" name="notes">
                    <input id="notes" name="notes" class="input" placeholder="Sonno, energia, fastidi..." value="{{ old('notes') }}">
                </x-field>
            </div>
            <input type="hidden" name="duration_minutes" data-duration>
            <button class="btn btn-rosso h-12 text-base"><x-icon name="check" size="18" /> Salva allenamento</button>
        </section>
    </form>

    {{-- Timer di recupero (compare quando premi ✓) --}}
    <div class="hidden fixed z-40 left-1/2 -translate-x-1/2 bottom-[calc(76px+env(safe-area-inset-bottom))] lg:bottom-6 w-[min(440px,calc(100%-32px))] bg-inchiostro text-white rounded-md overflow-hidden" data-rest-box role="timer" aria-live="polite">
        <div class="h-1 bg-white/15"><div class="h-full bg-disco-rosso transition-[width] duration-1000 ease-linear" data-rest-bar></div></div>
        <div class="flex items-center gap-3 px-4 py-3">
            <span class="etichetta !text-white/60">Recupero</span>
            <span class="numero text-4xl mr-auto" data-rest-time>1:30</span>
            <button type="button" class="btn btn-piccolo !border-white/30 !text-white hover:!bg-white hover:!text-inchiostro" data-rest-plus>+15s</button>
            <button type="button" class="btn btn-piccolo !border-transparent !text-white/70 hover:!text-white hover:!bg-white/10" data-rest-skip>Salta</button>
        </div>
    </div>

    {{-- Modello per "+ Serie": il JS sostituisce __EX__ e __N__ --}}
    <template id="set-template">
        @include('train._set', ['exerciseId' => '__EX__', 'n' => '__N__', 'last' => null, 'target' => null])
    </template>
</x-layout>
