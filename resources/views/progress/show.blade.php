<x-layout :title="$exercise->name">
    <a href="{{ route('progress.index') }}" class="btn btn-ghost btn-sm -ml-3 mb-3"><x-icon name="arrow-left" size="16" /> Pesi</a>
    <h1 class="title text-[30px] lg:text-[40px] mb-5">{{ $exercise->name }}</h1>

    @php $latest = $rows->first(); @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
        <x-stat label="Ultima volta" :value="kg($latest['avg'])" unit="kg"><span>peso medio</span></x-stat>
        <x-stat label="Massimo" :value="kg($rows->max('max'))" unit="kg"><span>singola serie</span></x-stat>
        <x-stat label="Media" :value="kg(round($rows->avg('avg'), 1))" unit="kg"><span>di sempre</span></x-stat>
        <x-stat label="Allenamenti" :value="$rows->count()"><span>con questo esercizio</span></x-stat>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-3 items-start">
        <x-panel title="Andamento" subtitle="Linea: peso medio · punti: massimo">
            <div class="h-56 lg:h-72">
                <canvas data-chart="{{ json_encode(['type' => 'lift', 'points' => $chart]) }}" role="img" aria-label="Andamento del peso di {{ $exercise->name }}"></canvas>
            </div>
        </x-panel>

        <x-panel title="Ogni allenamento">
            <table class="table">
                <thead><tr><th>Data</th><th class="text-right">Medio</th><th class="text-right">Max</th></tr></thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td><a href="{{ route('history.show', $row['session_id']) }}" class="text-fg-2 hover:text-accent">{{ $row['date']->translatedFormat('j M Y') }}</a></td>
                            <td class="num text-right">{{ kg($row['avg']) }}</td>
                            <td class="num text-right text-fg-3">{{ kg($row['max']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-panel>
    </div>
</x-layout>
