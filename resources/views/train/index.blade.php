<x-layout title="Allenamento" heading="Allenamento" subtitle="Scegli la scheda di oggi e inizia a segnare le serie.">
    <x-slot:actions>
        <a href="{{ route('workouts.create') }}" class="btn"><x-icon name="plus" size="16" /> Nuova scheda</a>
    </x-slot:actions>

    @if ($workouts->isEmpty())
        <x-panel>
            <x-empty title="Non hai ancora schede" text="Una scheda contiene gli esercizi con serie, rep, peso e recupero." :href="route('workouts.create')" cta="Crea la prima" />
        </x-panel>
    @else
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach ($workouts as $i => $workout)
                <div @class(['pannello flex items-center gap-4 p-4', 'border-inchiostro' => $i === 0 && $workouts->count() > 1])>
                    <x-plate :color="$workout->color" :letter="$workout->initial()" size="lg" />
                    <a href="{{ route('train.show', $workout) }}" class="flex-1 min-w-0">
                        <span class="flex items-center gap-2">
                            <span class="font-semibold text-lg truncate">{{ $workout->name }}</span>
                            @if ($i === 0 && $workouts->count() > 1)<span class="etichetta !text-disco-rosso">tocca a questa</span>@endif
                        </span>
                        <span class="block text-[13px] text-grafite">{{ $workout->items_count }} esercizi · ~{{ $workout->avg_minutes }} min</span>
                        <span class="block text-[12px] text-polvere">{{ $workout->last_done_at ? 'Ultima volta '.\Carbon\Carbon::parse($workout->last_done_at)->diffForHumans() : 'Mai fatta' }}</span>
                    </a>
                    <a href="{{ route('workouts.edit', $workout) }}" class="btn btn-muto btn-piccolo !px-2" aria-label="Modifica {{ $workout->name }}"><x-icon name="edit" size="16" /></a>
                    <a href="{{ route('train.show', $workout) }}" class="btn btn-pieno !px-3" aria-label="Inizia {{ $workout->name }}"><x-icon name="play" size="14" /></a>
                </div>
            @endforeach
        </div>
    @endif
</x-layout>
