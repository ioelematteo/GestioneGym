<x-layout title="Coach" heading="Coach" subtitle="L'IA legge i tuoi allenamenti, ogni serie e le misure.">
    <div class="grid grid-cols-1 lg:grid-cols-[380px_1fr] gap-3 items-start">
        <x-panel>
            @unless ($configured)
                <p class="mb-4 card !bg-surface-2 !border-warn/30 px-3 py-2.5 text-[13px] text-fg-2">
                    Manca la chiave di DeepSeek: aggiungi <code class="text-warn">DEEPSEEK_API_KEY</code> nelle variabili d'ambiente.
                </p>
            @endunless

            <form method="POST" action="{{ route('coach.store') }}" class="flex flex-col gap-3" data-coach-form>
                @csrf
                <label for="question" class="sr-only">Domanda</label>
                <textarea id="question" name="question" rows="4" class="input" placeholder="Chiedi qualcosa, o lascia vuoto per un'analisi delle ultime settimane.">{{ old('question') }}</textarea>
                @error('question')<p class="error">{{ $message }}</p>@enderror
                <div class="flex flex-wrap gap-1.5">
                    @foreach (['Com\'è andata questa settimana?', 'Quali esercizi sono fermi?', 'Cosa faccio al prossimo allenamento?'] as $suggestion)
                        <button type="button" class="chip hover:text-fg hover:border-line-strong cursor-pointer" data-suggestion>{{ $suggestion }}</button>
                    @endforeach
                </div>
                <button class="btn btn-primary btn-lg w-full mt-1" data-coach-submit>Analizza</button>
                <p class="text-[12px] text-fg-3">Mando schede, allenamenti delle ultime {{ config('gymlog.coach.weeks_of_data') }} settimane, pesi e misure. Delle foto solo data e posa: DeepSeek non vede le immagini.</p>
            </form>
        </x-panel>

        <div class="flex flex-col gap-3">
            @forelse ($reports as $report)
                <article class="card p-4 sm:p-5">
                    <header class="mb-3 pb-3 border-b border-line">
                        <p class="text-[15px] text-fg" style="font-weight: 600">{{ $report->question }}</p>
                        <p class="text-[12px] text-fg-3 mt-0.5">{{ $report->created_at->translatedFormat('j M · H:i') }}</p>
                    </header>
                    <div class="prose-coach">{!! \Illuminate\Support\Str::markdown($report->answer, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                </article>
            @empty
                <x-panel><x-empty title="Ancora nessuna analisi" text="Fai la prima domanda: la risposta resta salvata qui." /></x-panel>
            @endforelse
        </div>
    </div>
</x-layout>
