<x-layout title="Pesi" heading="Pesi" subtitle="Peso medio delle serie, allenamento dopo allenamento.">
    @if ($lifts->isEmpty())
        <x-panel><x-empty title="Ancora nessun dato" text="Salva un allenamento e qui vedi come cambiano i pesi." :href="route('train.index')" cta="Allenati" /></x-panel>
    @else
        <div class="card">
            <div class="rows">
                @foreach ($lifts as $lift)
                    <a href="{{ route('progress.show', $lift['exercise_id']) }}" class="flex items-center gap-3 px-4 sm:px-5 py-3.5 hover:bg-surface-2/60 transition-colors first:rounded-t-[18px] last:rounded-b-[18px]">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[15px] truncate" style="font-weight: 580">{{ $lift['exercise'] }}</span>
                            <span class="block text-[12px] text-fg-3">{{ $lift['sessions'] }} volte · max {{ kg($lift['max']) }} kg</span>
                        </span>
                        <x-spark :values="$lift['series']" width="64" height="24" />
                        <span class="flex flex-col items-end gap-1 min-w-[4.5rem]">
                            <span class="num text-[20px] leading-none">{{ $lift['last_avg'] > 0 ? kg($lift['last_avg']) : 'CL' }}@if ($lift['last_avg'] > 0)<span class="text-[12px] text-fg-3 font-normal"> kg</span>@endif</span>
                            <x-trend :delta="$lift['change']" unit="%" />
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-layout>
