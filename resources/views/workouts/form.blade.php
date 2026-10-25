@php $editing = $workout->exists; @endphp
<x-layout :title="$editing ? 'Modifica scheda' : 'Nuova scheda'" :heading="$editing ? 'Modifica' : 'Nuova scheda'" subtitle="Gli esercizi nell'ordine in cui li fai, con il peso di lavoro.">

    <form method="POST" action="{{ $editing ? route('workouts.update', $workout) : route('workouts.store') }}" class="grid lg:grid-cols-[300px_1fr] gap-3 items-start" data-plan-editor>
        @csrf
        @if ($editing) @method('PUT') @endif

        <x-panel title="Scheda" class="flex flex-col gap-4">
            <div class="flex flex-col gap-4">
                <x-field label="Nome" name="name">
                    <input id="name" name="name" class="input" value="{{ old('name', $workout->name) }}" placeholder="es. Allenamento A" maxlength="100" required data-plan-name>
                </x-field>

                <fieldset class="campo">
                    <legend class="text-[13px] font-medium text-grafite mb-1.5">Colore del disco</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach (config('gymlog.colors') as $key => $label)
                            <label class="cursor-pointer" title="{{ $label }}">
                                <input type="radio" name="color" value="{{ $key }}" class="peer sr-only" @checked(old('color', $workout->color) === $key)>
                                <x-plate :color="$key" :letter="$workout->exists ? $workout->initial() : 'A'" class="opacity-40 peer-checked:opacity-100 peer-checked:outline-2 peer-checked:outline-offset-2 peer-checked:outline-inchiostro peer-focus-visible:outline-2 peer-focus-visible:outline-disco-rosso" data-plate-preview />
                                <span class="sr-only">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color')<p class="errore">{{ $message }}</p>@enderror
                </fieldset>

                <x-field label="Note (facoltative)" name="notes">
                    <input id="notes" name="notes" class="input" value="{{ old('notes', $workout->notes) }}" maxlength="255">
                </x-field>
            </div>
        </x-panel>

        <x-panel title="Esercizi" subtitle="Scrivi il nome: se esiste te lo suggerisco, se è nuovo lo creo.">
            @error('rows')<p class="errore mb-3">{{ $message }}</p>@enderror
            @if ($errors->has('rows.*'))<p class="errore mb-3">{{ $errors->first('rows.*') }}</p>@endif

            <div class="flex flex-col gap-2" data-rows>
                @foreach ($rows as $i => $row)
                    @include('workouts._row', ['i' => $i, 'row' => $row])
                @endforeach
            </div>
            <button type="button" class="btn w-full mt-3 border-dashed" data-add-row><x-icon name="plus" size="16" /> Aggiungi esercizio</button>
        </x-panel>

        <div class="lg:col-start-2 flex flex-wrap gap-2">
            <button class="btn btn-pieno h-12 px-6">{{ $editing ? 'Salva modifiche' : 'Crea scheda' }}</button>
            <a href="{{ route('train.index') }}" class="btn btn-muto h-12">Annulla</a>
        </div>
    </form>

    @if ($editing)
        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border border-dashed border-disco-rosso/40 rounded-md p-4">
            <p class="text-sm text-grafite"><b class="text-inchiostro">Elimina scheda.</b> Sparisce dalla lista, lo storico resta.</p>
            <x-delete-button :action="route('workouts.destroy', $workout)" :confirm="'Eliminare «'.$workout->name.'»?'" />
        </div>
    @endif

    <datalist id="catalog">
        @foreach ($catalog as $name)<option value="{{ $name }}"></option>@endforeach
    </datalist>

    <template id="row-template">
        @include('workouts._row', ['i' => '__I__', 'row' => []])
    </template>
</x-layout>
