<x-layout title="Corpo" heading="Corpo" subtitle="Peso, misure e foto: per vedere i cambiamenti che lo specchio non nota.">
    <div class="grid lg:grid-cols-3 gap-3 items-start">

        {{-- Nuova misurazione --}}
        <x-panel title="Misurazione di oggi">
            <form method="POST" action="{{ route('body.store') }}" class="flex flex-col gap-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Peso (kg)" name="weight_kg">
                        <input id="weight_kg" name="weight_kg" class="input numero !text-2xl" inputmode="decimal" value="{{ old('weight_kg') }}" placeholder="58.4" required>
                    </x-field>
                    <x-field label="Data" name="measured_on">
                        <input id="measured_on" type="date" name="measured_on" class="input" value="{{ old('measured_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                    </x-field>
                    <x-field label="% grasso" name="body_fat">
                        <input id="body_fat" name="body_fat" class="input font-mono" inputmode="decimal" value="{{ old('body_fat') }}" placeholder="facoltativo">
                    </x-field>
                    <x-field label="Vita (cm)" name="waist_cm">
                        <input id="waist_cm" name="waist_cm" class="input font-mono" inputmode="decimal" value="{{ old('waist_cm') }}" placeholder="facoltativo">
                    </x-field>
                </div>
                <x-field label="Note" name="notes">
                    <input id="notes" name="notes" class="input" value="{{ old('notes') }}" placeholder="es. a digiuno, dopo il weekend">
                </x-field>
                <button class="btn btn-pieno">Salva misurazione</button>
                <p class="text-[12px] text-polvere">Una al giorno: se pesi di nuovo oggi, sostituisce quella di prima.</p>
            </form>
        </x-panel>

        {{-- Grafico peso --}}
        <x-panel title="Peso corporeo" subtitle="Ultimi 6 mesi" class="lg:col-span-2">
            @if ($chart->count() >= 2)
                <div class="h-64"><canvas data-chart="{{ json_encode(['type' => 'body', 'points' => $chart]) }}" role="img" aria-label="Andamento del peso corporeo"></canvas></div>
            @else
                <x-empty title="Servono almeno due misurazioni" text="Pesati con regolarità (stesso orario, stesse condizioni): il grafico arriva da solo." />
            @endif
        </x-panel>

        {{-- Foto --}}
        <x-panel title="Foto dei progressi" subtitle="Restano private: le vedi solo tu." class="lg:col-span-2">
            @forelse ($photos as $date => $group)
                <div class="py-3 border-t border-riga first:border-t-0">
                    <p class="etichetta mb-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('j F Y') }}</p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        @foreach ($group as $photo)
                            <figure class="relative group">
                                <a href="{{ route('photos.show', $photo) }}" target="_blank">
                                    <img src="{{ route('photos.show', $photo) }}" alt="Foto {{ $photo->pose }} del {{ $photo->taken_on->format('d/m/Y') }}" loading="lazy" class="aspect-[3/4] w-full object-cover rounded-sm bg-cemento">
                                </a>
                                <figcaption class="flex items-center justify-between mt-1 text-[12px] text-grafite">
                                    <span class="capitalize">{{ $photo->pose }}</span>
                                    <x-delete-button :action="route('photos.destroy', $photo)" confirm="Eliminare questa foto?" icon />
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            @empty
                <x-empty title="Nessuna foto" text="Una foto ogni 2-4 settimane, stessa luce e stessa posa: è il modo più onesto di vedere i progressi." />
            @endforelse
        </x-panel>

        {{-- Carica foto --}}
        <x-panel title="Carica foto">
            <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3">
                @csrf
                <x-field label="Foto (fino a 6)" name="photos">
                    <input id="photos" type="file" name="photos[]" accept="image/*" multiple required class="text-sm text-grafite file:mr-3 file:h-9 file:px-3 file:rounded-sm file:border file:border-inchiostro file:bg-transparent file:text-inchiostro file:font-medium file:cursor-pointer hover:file:bg-inchiostro hover:file:text-white">
                </x-field>
                @if ($errors->has('photos.*'))<p class="errore">{{ $errors->first('photos.*') }}</p>@endif
                <div class="grid grid-cols-2 gap-3">
                    <x-field label="Posa" name="pose">
                        <select id="pose" name="pose" class="input">
                            @foreach (\App\Models\ProgressPhoto::POSES as $pose)
                                <option value="{{ $pose }}" @selected(old('pose') === $pose)>{{ ucfirst($pose) }}</option>
                            @endforeach
                        </select>
                    </x-field>
                    <x-field label="Data" name="taken_on">
                        <input id="taken_on" type="date" name="taken_on" class="input" value="{{ old('taken_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}">
                    </x-field>
                </div>
                <x-field label="Note" name="notes">
                    <input id="notes_photo" name="notes" class="input" placeholder="es. mattina, a digiuno">
                </x-field>
                <button class="btn btn-pieno"><x-icon name="camera" size="16" /> Carica</button>
            </form>
        </x-panel>

        {{-- Storico misure --}}
        <x-panel title="Misurazioni" class="lg:col-span-3">
            @if ($entries->isEmpty())
                <x-empty title="Nessuna misurazione" text="Inizia dal modulo qui sopra." />
            @else
                <div class="overflow-x-auto">
                    <table class="tabella min-w-[520px]">
                        <thead><tr><th>Data</th><th class="text-right">Peso</th><th class="text-right">% grasso</th><th class="text-right">Vita</th><th class="pl-6">Note</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($entries as $entry)
                                <tr>
                                    <td>{{ $entry->measured_on->translatedFormat('j M Y') }}</td>
                                    <td class="font-mono text-right">{{ kg($entry->weight_kg) }} kg</td>
                                    <td class="font-mono text-right text-grafite">{{ $entry->body_fat ? kg($entry->body_fat).'%' : '—' }}</td>
                                    <td class="font-mono text-right text-grafite">{{ $entry->waist_cm ? kg($entry->waist_cm).' cm' : '—' }}</td>
                                    <td class="text-grafite pl-6">{{ $entry->notes }}</td>
                                    <td class="text-right"><x-delete-button :action="route('body.destroy', $entry)" confirm="Eliminare questa misurazione?" icon /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-panel>
    </div>
</x-layout>
