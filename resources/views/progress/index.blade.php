<x-layout title="Pesi" heading="Pesi" subtitle="Per ogni esercizio, il peso medio delle tue serie allenamento dopo allenamento.">
    @if ($lifts->isEmpty())
        <x-panel><x-empty title="Ancora nessun dato" text="Salva un allenamento e qui vedrai come cambiano i tuoi pesi." :href="route('train.index')" cta="Allenati" /></x-panel>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($lifts as $lift)
                <a href="{{ route('progress.show', $lift['exercise_id']) }}" class="pannello p-4 flex flex-col gap-3 hover:border-inchiostro transition-colors">
                    <span class="flex items-start justify-between gap-2">
                        <span class="font-semibold leading-tight">{{ $lift['exercise'] }}</span>
                        <x-trend :delta="$lift['change']" unit="%" />
                    </span>
                    <span class="flex items-end justify-between gap-3">
                        <span>
                            <span class="numero text-5xl">{{ $lift['last_avg'] > 0 ? kg($lift['last_avg']) : 'CL' }}</span>
                            @if ($lift['last_avg'] > 0)<span class="text-sm text-grafite"> kg</span>@endif
                        </span>
                        <x-spark :values="$lift['series']" width="110" height="34" class="text-grafite" />
                    </span>
                    <span class="flex justify-between font-mono text-[11px] text-polvere uppercase tracking-wider">
                        <span>primo {{ kg($lift['first_avg']) }}</span><span>max {{ kg($lift['max']) }}</span><span>{{ $lift['sessions'] }} volte</span>
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</x-layout>
