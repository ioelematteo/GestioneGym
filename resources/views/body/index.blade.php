<x-layout title="Corpo" heading="Corpo" subtitle="Peso, misure e foto dei progressi.">
    <div class="grid grid-cols-1 lg:grid-cols-[360px_1fr] gap-3 items-start">

        {{-- Pesata veloce: il campo che usi di più, in cima --}}
        <x-panel title="Pesata di oggi">
            <form method="POST" action="{{ route('body.store') }}" class="flex flex-col gap-3">
                @csrf
                <div class="flex gap-2">
                    <label for="weight_kg" class="sr-only">Peso in kg</label>
                    <input id="weight_kg" name="weight_kg" class="input num !h-14 !text-[24px] flex-1" inputmode="decimal" value="{{ old('weight_kg') }}" placeholder="{{ $entries->first() ? kg($entries->first()->weight_kg) : '0.0' }}" required>
                    <button class="btn btn-primary !h-14 !px-6">Salva</button>
                </div>
                @error('weight_kg')<p class="error">{{ $message }}</p>@enderror

                {{-- il resto è facoltativo: chiuso per non disturbare --}}
                <details class="group" @if ($errors->hasAny(['body_fat', 'waist_cm', 'measured_on', 'notes'])) open @endif>
                    <summary class="list-none [&::-webkit-details-marker]:hidden flex items-center gap-1.5 text-[13px] text-fg-3 cursor-pointer py-1">
                        <x-icon name="chevron-down" size="14" class="transition group-open:rotate-180" /> % grasso, vita, data, note
                    </summary>
                    <div class="grid grid-cols-2 gap-3 pt-3">
                        <x-field label="% grasso" name="body_fat"><input id="body_fat" name="body_fat" class="input num" inputmode="decimal" value="{{ old('body_fat') }}"></x-field>
                        <x-field label="Vita (cm)" name="waist_cm"><input id="waist_cm" name="waist_cm" class="input num" inputmode="decimal" value="{{ old('waist_cm') }}"></x-field>
                        <x-field label="Data" name="measured_on" class="col-span-2"><input id="measured_on" type="date" name="measured_on" class="input" value="{{ old('measured_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}"></x-field>
                        <x-field label="Note" name="notes" class="col-span-2"><input id="notes" name="notes" class="input" value="{{ old('notes') }}" placeholder="es. a digiuno"></x-field>
                    </div>
                </details>
                <p class="text-[12px] text-fg-3">Una al giorno: se ti ripesi, sostituisce quella di oggi.</p>
            </form>
        </x-panel>

        <x-panel title="Andamento" subtitle="Ultimi 6 mesi">
            @if ($chart->count() >= 2)
                <div class="h-52 lg:h-64"><canvas data-chart="{{ json_encode(['type' => 'line', 'unit' => 'kg', 'points' => $chart]) }}" role="img" aria-label="Andamento del peso corporeo"></canvas></div>
            @else
                <x-empty title="Servono almeno due pesate" text="Pesati alla stessa ora e nelle stesse condizioni." />
            @endif
        </x-panel>

        {{-- Foto --}}
        <x-panel title="Foto" subtitle="Private: le vedi solo tu." class="lg:col-span-2">
            <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data" class="flex flex-col gap-3 mb-2" data-photo-form>
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-[1fr_1fr_auto] gap-2">
                    <select name="pose" class="input" aria-label="Posa">
                        @foreach (\App\Models\ProgressPhoto::POSES as $pose)
                            <option value="{{ $pose }}" @selected(old('pose') === $pose)>{{ ucfirst($pose) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="taken_on" class="input" value="{{ old('taken_on', now()->toDateString()) }}" max="{{ now()->toDateString() }}" aria-label="Data">
                    {{-- il vero input file è nascosto: il bottone apre fotocamera/galleria e il form parte da solo --}}
                    <label class="btn btn-primary col-span-2 sm:col-span-1 cursor-pointer">
                        <x-icon name="camera" size="17" /> <span data-photo-label>Aggiungi foto</span>
                        <input type="file" name="photos[]" accept="image/*" multiple class="sr-only" data-photo-input>
                    </label>
                </div>
                @if ($errors->hasAny(['photos', 'photos.*', 'pose', 'taken_on']))
                    <p class="error">{{ $errors->first('photos') ?: $errors->first('photos.*') ?: $errors->first('pose') ?: $errors->first('taken_on') }}</p>
                @endif
            </form>

            @forelse ($photos as $date => $group)
                <div class="pt-4">
                    <p class="text-[13px] text-fg-3 mb-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('j F Y') }}</p>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                        @foreach ($group as $photo)
                            <figure class="relative">
                                <a href="{{ route('photos.show', $photo) }}" target="_blank" class="block">
                                    <img src="{{ route('photos.show', $photo) }}" alt="Foto {{ $photo->pose }} del {{ $photo->taken_on->format('d/m/Y') }}" loading="lazy" decoding="async" class="aspect-[3/4] w-full object-cover rounded-xl bg-surface-2">
                                </a>
                                <figcaption class="absolute inset-x-1.5 bottom-1.5 flex items-center justify-between">
                                    <span class="chip !h-6 !bg-bg/70 !border-transparent backdrop-blur capitalize">{{ $photo->pose }}</span>
                                    <x-delete-button :action="route('photos.destroy', $photo)" confirm="Eliminare questa foto?" icon class="!size-8 !bg-bg/70 backdrop-blur" />
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            @empty
                <x-empty title="Nessuna foto" text="Una ogni 2–4 settimane, stessa luce e stessa posa." />
            @endforelse
        </x-panel>

        {{-- Storico misure: le prime 6, le altre dietro "Mostra tutte" (solo CSS: checkbox + has-[]) --}}
        <x-panel title="Misurazioni" class="lg:col-span-2 group">
            @if ($entries->isEmpty())
                <x-empty title="Nessuna misurazione" text="Inizia dalla pesata di oggi." />
            @else
                <div class="rows -mx-4 sm:-mx-5 -mb-2">
                    @foreach ($entries as $i => $entry)
                        <div @class(['items-center gap-3 px-4 sm:px-5 py-2.5', 'flex' => $i < 6, 'hidden group-has-[#all-entries:checked]:flex' => $i >= 6])>
                            <span class="text-[14px] text-fg-2 w-24 shrink-0">{{ $entry->measured_on->translatedFormat('j M Y') }}</span>
                            <span class="num text-[16px]">{{ kg($entry->weight_kg) }}<span class="text-[11px] text-fg-3 font-normal"> kg</span></span>
                            <span class="flex-1 min-w-0 truncate text-[13px] text-fg-3">
                                {{ collect([$entry->body_fat ? kg($entry->body_fat).'%' : null, $entry->waist_cm ? kg($entry->waist_cm).' cm' : null, $entry->notes])->filter()->implode(' · ') }}
                            </span>
                            <x-delete-button :action="route('body.destroy', $entry)" confirm="Eliminare questa misurazione?" icon />
                        </div>
                    @endforeach
                    @if ($entries->count() > 6)
                        <label class="flex justify-center py-3 text-[14px] text-fg-2 hover:text-fg cursor-pointer group-has-[#all-entries:checked]:hidden">
                            <input type="checkbox" id="all-entries" class="sr-only"> Mostra tutte ({{ $entries->count() }})
                        </label>
                    @endif
                </div>
            @endif
        </x-panel>
    </div>
</x-layout>
