<x-layout :title="$session->workout->name">
    <a href="{{ route('history.index') }}" class="inline-flex items-center gap-1.5 text-sm text-grafite hover:text-inchiostro mb-4"><x-icon name="arrow-left" size="14" /> Storico</a>

    <div class="flex flex-wrap items-center gap-4 mb-6">
        <x-plate :color="$session->workout->color" :letter="$session->workout->initial()" size="lg" />
        <div class="flex-1 min-w-0">
            <h1 class="titolo text-4xl sm:text-6xl break-words">{{ $session->workout->name }}</h1>
            <p class="text-grafite text-sm mt-1">{{ $session->performed_at->translatedFormat('l j F Y') }} · {{ $session->performed_at->diffForHumans() }}</p>
        </div>
        @unless ($session->workout->trashed())
            <a href="{{ route('train.show', $session->workout) }}" class="btn"><x-icon name="repeat" size="16" /> Rifallo</a>
        @endunless
    </div>

    <dl class="grid grid-cols-2 sm:grid-cols-4 pannello mb-3 divide-x divide-riga">
        @foreach (['Durata' => durata($session->duration_minutes), 'Volume' => volume_breve($session->volume()).' kg', 'Serie' => $session->sets->count(), 'Esercizi' => $exercises->count()] as $label => $value)
            <div class="p-4"><dt class="etichetta">{{ $label }}</dt><dd class="numero text-3xl mt-1">{{ $value }}</dd></div>
        @endforeach
    </dl>

    @if ($session->notes)
        <p class="mb-3 flex gap-2 border-l-4 border-disco-giallo bg-gesso px-4 py-3 text-sm"><x-icon name="note" size="16" class="mt-0.5" /> {{ $session->notes }}</p>
    @endif

    <div class="grid md:grid-cols-2 gap-3">
        @foreach ($exercises as $exerciseId => $sets)
            <x-panel :title="$sets->first()->exercise->name">
                @if (in_array($exerciseId, $records))
                    <x-slot:aside><span class="etichetta !text-white bg-disco-rosso px-2 py-1 rounded-sm">Record</span></x-slot:aside>
                @endif
                <table class="tabella">
                    <thead><tr><th class="w-8">#</th><th>Serie</th><th class="w-2/5">Stato</th></tr></thead>
                    <tbody>
                        @foreach ($sets as $set)
                            <tr>
                                <td class="font-mono text-polvere">{{ $set->set_number }}</td>
                                <td class="font-mono">{{ serie($set->weight, $set->reps) }}</td>
                                <td class="text-grafite">{{ $set->state?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-panel>
        @endforeach
    </div>

    <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border border-dashed border-disco-rosso/40 rounded-md p-4">
        <p class="text-sm text-grafite"><b class="text-inchiostro">Salvato per sbaglio?</b> Eliminandolo spariscono anche le sue serie.</p>
        <x-delete-button :action="route('history.destroy', $session)" confirm="Eliminare questo allenamento?" />
    </div>
</x-layout>
