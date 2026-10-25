<x-layout title="Allena" heading="Allena" subtitle="Scegli la scheda di oggi.">
    <x-slot:actions>
        <a href="{{ route('workouts.create') }}" class="btn btn-sm"><x-icon name="plus" size="16" /> Nuova scheda</a>
    </x-slot:actions>

    @if ($workouts->isEmpty())
        <x-panel>
            <x-empty title="Non hai ancora schede" text="Una scheda contiene gli esercizi con serie, rep, peso e recupero." :href="route('workouts.create')" cta="Crea la prima" />
        </x-panel>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($workouts as $i => $workout)
                @php $suggested = $i === 0 && $workouts->count() > 1; @endphp
                <div @class(['card flex items-center gap-3 p-3 pl-4', '!border-accent/30' => $suggested])>
                    <a href="{{ route('train.show', $workout) }}" class="flex items-center gap-3 min-w-0 flex-1 py-1">
                        <x-badge :color="$workout->color" :letter="$workout->initial()" size="lg" />
                        <span class="min-w-0 flex-1">
                            <span class="block text-[17px] truncate" style="font-weight: 620">{{ $workout->name }}</span>
                            <span class="block text-[13px] text-fg-3 truncate">@if ($suggested)<span class="text-accent" style="font-weight: 560">Oggi</span> · @endif{{ $workout->items_count }} esercizi · ~{{ $workout->avg_minutes }} min</span>
                            <span class="block text-[12px] text-fg-3">{{ $workout->last_done_at ? 'Ultima volta '.\Carbon\Carbon::parse($workout->last_done_at)->diffForHumans() : 'Mai fatta' }}</span>
                        </span>
                    </a>
                    <a href="{{ route('workouts.edit', $workout) }}" class="btn btn-ghost btn-icon" aria-label="Modifica {{ $workout->name }}"><x-icon name="edit" size="18" /></a>
                    <a href="{{ route('train.show', $workout) }}" class="btn btn-primary btn-icon !size-12" aria-label="Inizia {{ $workout->name }}"><x-icon name="play" size="16" /></a>
                </div>
            @endforeach
        </div>
    @endif
</x-layout>
