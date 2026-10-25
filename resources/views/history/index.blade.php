<x-layout title="Storico" heading="Storico" subtitle="Tutti i tuoi allenamenti.">
    <x-panel>
        <div class="rows -mx-4 sm:-mx-5 -my-2">
            @forelse ($sessions as $session)
                <a href="{{ route('history.show', $session) }}" class="flex items-center gap-3 px-4 sm:px-5 py-3.5 hover:bg-surface-2/60 transition-colors">
                    <x-badge :color="$session->workout->color" :letter="$session->workout->initial()" />
                    <span class="min-w-0 flex-1">
                        <span class="block text-[15px] truncate" style="font-weight: 580">{{ $session->workout->name }}</span>
                        <span class="block text-[13px] text-fg-3 first-letter:uppercase">{{ $session->performed_at->translatedFormat('D j M Y') }} · {{ durata($session->duration_minutes) }}</span>
                    </span>
                    <span class="text-right">
                        <span class="block num text-[16px]">{{ volume_breve($session->volume()) }}<span class="text-[11px] text-fg-3 font-normal"> kg</span></span>
                        <span class="block text-[12px] text-fg-3">{{ $session->sets->count() }} serie</span>
                    </span>
                </a>
            @empty
                <div class="px-4 sm:px-5"><x-empty title="Lo storico è vuoto" text="Ogni allenamento che salvi finisce qui." :href="route('train.index')" cta="Allenati" /></div>
            @endforelse
        </div>
    </x-panel>

    <div class="mt-4">{{ $sessions->links('partials.pager') }}</div>
</x-layout>
