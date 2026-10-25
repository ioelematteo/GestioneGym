<x-layout title="Storico" heading="Storico" subtitle="Tutti gli allenamenti, dal più recente.">
    <x-panel>
        @forelse ($sessions as $session)
            <a href="{{ route('history.show', $session) }}" class="flex items-center gap-4 py-3 border-t border-riga first:border-t-0 hover:bg-cemento/60 -mx-2 px-2 rounded-sm">
                <x-plate :color="$session->workout->color" :letter="$session->workout->initial()" />
                <span class="flex-1 min-w-0">
                    <span class="block font-medium truncate">{{ $session->workout->name }}</span>
                    <span class="block text-[13px] text-grafite">{{ $session->performed_at->translatedFormat('l j F Y') }} · {{ durata($session->duration_minutes) }}</span>
                </span>
                <span class="text-right">
                    <span class="block numero text-2xl">{{ volume_breve($session->volume()) }}</span>
                    <span class="etichetta">kg · {{ $session->sets->count() }} serie</span>
                </span>
            </a>
        @empty
            <x-empty title="Lo storico è vuoto" text="Ogni allenamento che salvi finisce qui, con tutte le serie." :href="route('train.index')" cta="Allenati" />
        @endforelse

        <div class="mt-4">{{ $sessions->links() }}</div>
    </x-panel>
</x-layout>
