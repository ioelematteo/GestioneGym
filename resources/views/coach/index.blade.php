<x-layout title="Coach" heading="Coach" subtitle="L'IA legge i tuoi allenamenti, i pesi di ogni serie e le misure, e ti dice cosa nota.">
    <div class="grid lg:grid-cols-[380px_1fr] gap-3 items-start">
        <x-panel title="Chiedi al coach">
            @unless ($configured)
                <p class="mb-3 border-l-4 border-disco-giallo bg-cemento px-3 py-2 text-[13px]">
                    Manca la chiave di DeepSeek. Aggiungi <code class="font-mono">DEEPSEEK_API_KEY</code> nel file <code class="font-mono">.env</code> (o nelle variabili d'ambiente di Laravel Cloud).
                </p>
            @endunless

            <form method="POST" action="{{ route('coach.store') }}" class="flex flex-col gap-3" data-coach-form>
                @csrf
                <x-field label="Domanda (facoltativa)" name="question" hint="Vuota = analisi generale delle ultime settimane.">
                    <textarea id="question" name="question" rows="4" class="input" placeholder="es. Perché la panca inclinata è ferma? Devo aumentare il peso?">{{ old('question') }}</textarea>
                </x-field>
                <div class="flex flex-wrap gap-1.5">
                    @foreach (['Com\'è andata questa settimana?', 'Quali esercizi sono fermi?', 'Cosa faccio al prossimo allenamento?'] as $suggestion)
                        <button type="button" class="btn btn-piccolo !h-7 !text-[12px] !border-riga" data-suggestion>{{ $suggestion }}</button>
                    @endforeach
                </div>
                <button class="btn btn-rosso h-11" data-coach-submit>Analizza i miei dati</button>
                <p class="text-[12px] text-polvere">Mando: schede, allenamenti delle ultime {{ config('gymlog.coach.weeks_of_data') }} settimane (ogni serie), andamento dei pesi, misure. Delle foto solo data, posa e note: DeepSeek non vede le immagini.</p>
            </form>
        </x-panel>

        <div class="flex flex-col gap-3">
            @forelse ($reports as $report)
                <article class="pannello p-5">
                    <header class="flex flex-wrap items-baseline justify-between gap-2 mb-3 pb-3 border-b border-riga">
                        <p class="font-semibold">{{ $report->question }}</p>
                        <p class="etichetta">{{ $report->created_at->translatedFormat('j M · H:i') }} · {{ $report->model }}</p>
                    </header>
                    <div class="coach-risposta text-[15px] leading-relaxed max-w-prose">{!! \Illuminate\Support\Str::markdown($report->answer, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                </article>
            @empty
                <x-panel><x-empty title="Ancora nessuna analisi" text="Fai la prima domanda: la risposta resta salvata qui." /></x-panel>
            @endforelse
        </div>
    </div>
</x-layout>
