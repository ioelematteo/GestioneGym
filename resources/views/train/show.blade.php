<x-layout :title="$workout->name" focus>
    {{-- Barra in alto della modalità palestra: indietro, nome, cronometro --}}
    <div class="sticky top-0 z-30 -mx-4 lg:-mx-10 -mt-5 lg:-mt-10 mb-4 px-4 lg:px-10 pt-[env(safe-area-inset-top)] bg-bg/85 backdrop-blur-xl border-b border-line/60">
        <div class="flex items-center gap-3 h-16">
            <a href="{{ route('train.index') }}" class="btn btn-ghost btn-icon -ml-2" aria-label="Torna alle schede"><x-icon name="arrow-left" size="20" /></a>
            <x-badge :color="$workout->color" :letter="$workout->initial()" size="sm" />
            <span class="min-w-0 flex-1">
                <span class="block text-[16px] leading-tight truncate" style="font-weight: 640">{{ $workout->name }}</span>
                <span class="block text-[12px] text-fg-3 truncate">{{ $last ? 'Ultima volta '.$last->performed_at->diffForHumans() : 'Prima volta' }}</span>
            </span>
            <span class="flex items-center gap-1.5 h-9 px-3 rounded-full bg-surface-2 border border-line" title="Da quanto ti stai allenando">
                <x-icon name="timer" size="15" class="text-accent" /><span class="num tabular-nums text-[15px] min-w-[3.6ch] text-right" data-clock>0:00</span>
            </span>
        </div>
    </div>

    <div class="hidden card mb-3 items-center gap-3 px-4 py-3 text-[14px] !border-warn/30" data-draft-notice>
        <x-icon name="repeat" size="16" class="text-warn shrink-0" />
        <span class="flex-1">Ho ripreso l'allenamento lasciato a metà.</span>
        <button type="button" class="btn btn-ghost btn-sm -mr-2" data-draft-discard>Ricomincia</button>
    </div>

    @if ($last?->notes)
        <p class="card mb-3 flex gap-3 px-4 py-3 text-[14px] text-fg-2"><x-icon name="note" size="16" class="text-warn mt-0.5 shrink-0" /> <span><span class="text-fg">Ultima volta:</span> {{ $last->notes }}</span></p>
    @endif

    @error('sets')
        <p class="card mb-3 px-4 py-3 text-[14px] !border-danger/40 text-danger" role="alert">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('train.store', $workout) }}" id="session-form" class="grid grid-cols-1 lg:grid-cols-2 gap-3 items-start" data-session="{{ $workout->id }}">
        @csrf

        @foreach ($workout->items as $item)
            @php
            $previous = $lastSets[$item->exercise_id] ?? []; // le serie di quest'esercizio l'ultima volta
            $rows = max($item->sets, $previous ? max(array_keys($previous)) : 0);
            @endphp
            <section class="card px-4 pt-4 pb-2" data-exercise="{{ $item->exercise_id }}" data-rest="{{ $item->rest_seconds }}">
                <header class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-[18px] leading-tight" style="font-weight: 640">{{ $item->exercise->name }}</h2>
                        <p class="num text-[13px] text-fg-3 mt-1" style="font-weight: 500">{{ $item->sets }} × {{ $item->reps_target ?: '—' }} · rec {{ gmdate('i:s', $item->rest_seconds) }}</p>
                    </div>
                    @if ($item->weight_target !== null)
                        <span class="chip num shrink-0 !text-fg-2">{{ $item->weight_target > 0 ? kg($item->weight_target).' kg' : 'corpo libero' }}</span>
                    @endif
                </header>
                @if ($item->notes)<p class="text-[13px] text-fg-3 mt-2">{{ $item->notes }}</p>@endif

                <div class="grid grid-cols-[20px_1fr_1fr_56px] gap-x-2 mt-4 text-[11px] text-fg-3 text-center" aria-hidden="true"><span></span><span>kg</span><span>rep</span><span></span></div>

                <div class="rows" data-set-list>
                    @for ($n = 1; $n <= $rows; $n++)
                        @include('train._set', ['exerciseId' => $item->exercise_id, 'n' => $n, 'last' => $previous[$n] ?? null, 'target' => $item->weight_target])
                    @endfor
                </div>

                <button type="button" class="btn btn-ghost btn-sm w-full mt-1 mb-1" data-add-set><x-icon name="plus" size="15" /> Aggiungi serie</button>
            </section>
        @endforeach

        <section class="card p-4 lg:col-span-2 flex flex-col gap-4">
            <div>
                <h2 class="text-[16px]" style="font-weight: 620">Chiudi allenamento</h2>
                <p class="text-[13px] text-fg-3 mt-0.5">Si salvano solo le serie con le rep scritte.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <x-field label="Data" name="date">
                    <input id="date" type="date" name="date" class="input" value="{{ old('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                </x-field>
                <x-field label="Peso corporeo" name="body_weight">
                    <input id="body_weight" name="body_weight" class="input num" inputmode="decimal" placeholder="kg" value="{{ old('body_weight') }}">
                </x-field>
                <x-field label="Note" name="notes" class="col-span-2 sm:col-span-1">
                    <input id="notes" name="notes" class="input" placeholder="Sonno, energia, fastidi…" value="{{ old('notes') }}">
                </x-field>
            </div>
            <input type="hidden" name="duration_minutes" data-duration>
        </section>
    </form>

    {{-- Timer di recupero + Salva: sempre sotto il pollice --}}
    <div class="fixed inset-x-0 bottom-0 z-30 lg:left-[248px] px-4 pb-[calc(env(safe-area-inset-bottom)+12px)] pt-3 bg-gradient-to-t from-bg via-bg/95 to-transparent pointer-events-none">
        <div class="max-w-[640px] mx-auto flex flex-col gap-2 pointer-events-auto">
            <div class="hidden card overflow-hidden !bg-surface-2 !border-line-strong" data-rest-box role="timer" aria-live="polite">
                <div class="h-1 bg-surface-3"><div class="h-full bg-accent transition-[width] duration-1000 ease-linear" data-rest-bar></div></div>
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <span class="text-[13px] text-fg-3">Recupero</span>
                    <span class="num tabular-nums text-[28px] leading-none mr-auto" data-rest-time>1:30</span>
                    <button type="button" class="btn btn-sm" data-rest-plus>+15s</button>
                    <button type="button" class="btn btn-ghost btn-sm" data-rest-skip>Salta</button>
                </div>
            </div>
            <button type="submit" form="session-form" class="btn btn-primary btn-lg w-full" data-save><x-icon name="check" size="18" /> Salva allenamento</button>
        </div>
    </div>

    {{-- Modello per "Aggiungi serie": il JS sostituisce __EX__ e __N__ --}}
    <template id="set-template">
        @include('train._set', ['exerciseId' => '__EX__', 'n' => '__N__', 'last' => null, 'target' => null])
    </template>
</x-layout>
