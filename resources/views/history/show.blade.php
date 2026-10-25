<x-layout :title="$session->workout->name">
    <a href="{{ route('history.index') }}" class="btn btn-ghost btn-sm -ml-3 mb-3"><x-icon name="arrow-left" size="16" /> Storico</a>

    <div class="flex items-center gap-3 mb-5">
        <x-badge :color="$session->workout->color" :letter="$session->workout->initial()" size="lg" />
        <div class="min-w-0 flex-1">
            <h1 class="title text-[26px] lg:text-[36px] truncate">{{ $session->workout->name }}</h1>
            <p class="text-[14px] text-fg-3 first-letter:uppercase">{{ $session->performed_at->translatedFormat('l j F Y') }}</p>
        </div>
        @unless ($session->workout->trashed())
            <a href="{{ route('train.show', $session->workout) }}" class="btn btn-sm shrink-0"><x-icon name="repeat" size="15" /> Rifai</a>
        @endunless
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
        @foreach (['Durata' => durata($session->duration_minutes), 'Volume' => volume_breve($session->volume()).' kg', 'Serie' => $session->sets->count(), 'Esercizi' => $exercises->count()] as $label => $value)
            <div class="card p-4"><p class="text-[13px] text-fg-3">{{ $label }}</p><p class="num text-[22px] mt-1">{{ $value }}</p></div>
        @endforeach
    </div>

    @if ($session->notes)
        <p class="card mb-3 flex gap-3 px-4 py-3 text-[14px] text-fg-2"><x-icon name="note" size="16" class="text-warn mt-0.5 shrink-0" /> {{ $session->notes }}</p>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach ($exercises as $exerciseId => $sets)
            <x-panel :title="$sets->first()->exercise->name">
                @if (in_array($exerciseId, $records))
                    <x-slot:aside><span class="chip !text-accent !border-accent/25 !bg-accent/10">Record</span></x-slot:aside>
                @endif
                <div class="rows">
                    @foreach ($sets as $set)
                        <div class="flex items-center gap-3 py-2.5">
                            <span class="num text-[13px] text-fg-3 w-5">{{ $set->set_number }}</span>
                            <span class="num text-[16px] flex-1">{{ serie($set->weight, $set->reps) }}</span>
                            @if ($set->state)<span class="chip">{{ $set->state->name }}</span>@endif
                        </div>
                    @endforeach
                </div>
            </x-panel>
        @endforeach
    </div>

    <div class="card mt-8 flex flex-wrap items-center justify-between gap-3 p-4 !border-danger/25">
        <p class="text-[14px] text-fg-3"><span class="text-fg">Salvato per sbaglio?</span> Si cancellano anche le serie.</p>
        <x-delete-button :action="route('history.destroy', $session)" confirm="Eliminare questo allenamento?" />
    </div>
</x-layout>
