<x-layout :title="$exercise->name">
    <a href="{{ route('progress.index') }}" class="inline-flex items-center gap-1.5 text-sm text-grafite hover:text-inchiostro mb-4"><x-icon name="arrow-left" size="14" /> Pesi</a>
    <h1 class="titolo text-5xl sm:text-6xl mb-6">{{ $exercise->name }}</h1>

    @php $latest = $rows->first(); @endphp
    <dl class="grid grid-cols-2 sm:grid-cols-4 pannello mb-3 divide-x divide-riga">
        <div class="p-4"><dt class="etichetta">Peso medio ultima volta</dt><dd class="numero text-4xl mt-1">{{ kg($latest['avg']) }}<span class="text-sm font-sans font-normal text-grafite"> kg</span></dd></div>
        <div class="p-4"><dt class="etichetta">Media di sempre</dt><dd class="numero text-4xl mt-1">{{ kg(round($rows->avg('avg'), 1)) }}<span class="text-sm font-sans font-normal text-grafite"> kg</span></dd></div>
        <div class="p-4"><dt class="etichetta">Peso massimo</dt><dd class="numero text-4xl mt-1">{{ kg($rows->max('max')) }}<span class="text-sm font-sans font-normal text-grafite"> kg</span></dd></div>
        <div class="p-4"><dt class="etichetta">Allenamenti</dt><dd class="numero text-4xl mt-1">{{ $rows->count() }}</dd></div>
    </dl>

    <div class="grid lg:grid-cols-[1.4fr_1fr] gap-3 items-start">
        <x-panel title="Andamento" subtitle="Peso medio (linea) e peso massimo (punti) di ogni allenamento">
            <div class="h-72">
                <canvas data-chart="{{ json_encode(['type' => 'lift', 'points' => $chart]) }}" role="img" aria-label="Andamento del peso di {{ $exercise->name }}"></canvas>
            </div>
        </x-panel>

        <x-panel title="Allenamenti">
            <table class="tabella">
                <thead><tr><th>Data</th><th class="text-right">Medio</th><th class="text-right">Max</th><th class="text-right">Serie</th></tr></thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td><a href="{{ route('history.show', $row['session_id']) }}" class="hover:text-disco-rosso">{{ $row['date']->translatedFormat('j M Y') }}</a></td>
                            <td class="font-mono text-right">{{ kg($row['avg']) }}</td>
                            <td class="font-mono text-right text-grafite">{{ kg($row['max']) }}</td>
                            <td class="font-mono text-right text-polvere">{{ $row['sets'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-panel>
    </div>
</x-layout>
