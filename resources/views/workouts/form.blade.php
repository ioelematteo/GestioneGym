@php $editing = $workout->exists; @endphp
<x-layout :title="$editing ? 'Modifica scheda' : 'Nuova scheda'" :heading="$editing ? 'Modifica scheda' : 'Nuova scheda'" subtitle="Esercizi nell'ordine in cui li fai, con il peso di lavoro.">

    <form method="POST" action="{{ $editing ? route('workouts.update', $workout) : route('workouts.store') }}" class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-3 items-start" data-plan-editor>
        @csrf
        @if ($editing) @method('PUT') @endif

        <x-panel title="Scheda">
            <div class="flex flex-col gap-4">
                <x-field label="Nome" name="name">
                    <input id="name" name="name" class="input" value="{{ old('name', $workout->name) }}" placeholder="es. Allenamento A" maxlength="100" required data-plan-name>
                </x-field>

                <fieldset class="field">
                    <legend class="text-[13px] text-fg-2 mb-2" style="font-weight: 520">Colore</legend>
                    <div class="flex flex-wrap gap-2">
                        @foreach (config('gymlog.colors') as $key => $label)
                            <label class="cursor-pointer" title="{{ $label }}">
                                <input type="radio" name="color" value="{{ $key }}" class="peer sr-only" @checked(old('color', $workout->color ?? 'verde') === $key)>
                                <x-badge :color="$key" :letter="$editing ? $workout->initial() : 'A'" class="opacity-40 transition peer-checked:opacity-100 peer-checked:ring-2 peer-checked:ring-fg peer-checked:ring-offset-2 peer-checked:ring-offset-surface peer-focus-visible:ring-2 peer-focus-visible:ring-accent" data-badge-preview />
                                <span class="sr-only">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color')<p class="error">{{ $message }}</p>@enderror
                </fieldset>

                <x-field label="Note" name="notes">
                    <input id="notes" name="notes" class="input" value="{{ old('notes', $workout->notes) }}" placeholder="Facoltative" maxlength="255">
                </x-field>
            </div>
        </x-panel>

        <x-panel title="Esercizi" subtitle="Se l'esercizio esiste te lo suggerisco, se è nuovo lo creo.">
            @error('rows')<p class="error mb-2">{{ $message }}</p>@enderror
            @if ($errors->has('rows.*'))<p class="error mb-2">{{ $errors->first('rows.*') }}</p>@endif

            <div class="rows -mt-2" data-rows>
                @foreach ($rows as $i => $row)
                    @include('workouts._row', ['i' => $i, 'row' => $row])
                @endforeach
            </div>
            <button type="button" class="btn w-full mt-1 !border-dashed" data-add-row><x-icon name="plus" size="16" /> Aggiungi esercizio</button>
        </x-panel>

        <div class="lg:col-start-2 flex gap-2">
            <button class="btn btn-primary btn-lg flex-1 sm:flex-none">{{ $editing ? 'Salva modifiche' : 'Crea scheda' }}</button>
            <a href="{{ route('train.index') }}" class="btn btn-ghost btn-lg">Annulla</a>
        </div>
    </form>

    @if ($editing)
        <div class="card mt-8 flex flex-wrap items-center justify-between gap-3 p-4 !border-danger/25">
            <p class="text-[14px] text-fg-3"><span class="text-fg">Elimina scheda.</span> Lo storico resta.</p>
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
