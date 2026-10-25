{{-- Una riga esercizio nel form scheda. $i = indice (o __I__ nel modello), $row = dati --}}
<div class="grid grid-cols-[1fr_auto] gap-3 p-3 border border-riga rounded-sm bg-white/60" data-row>
    <div class="grid grid-cols-2 sm:grid-cols-[2fr_64px_84px_84px_96px] gap-2.5">
        <div class="campo col-span-2 sm:col-span-1">
            <label>Esercizio</label>
            <input name="rows[{{ $i }}][name]" class="input" list="catalog" value="{{ $row['name'] ?? '' }}" placeholder="es. Panca inclinata" autocomplete="off" data-row-name>
        </div>
        <div class="campo">
            <label>Serie</label>
            <input name="rows[{{ $i }}][sets]" type="number" min="1" max="10" class="input font-mono" value="{{ $row['sets'] ?? 3 }}">
        </div>
        <div class="campo">
            <label>Rep</label>
            <input name="rows[{{ $i }}][reps_target]" class="input font-mono" value="{{ $row['reps_target'] ?? '' }}" placeholder="8-10" maxlength="20">
        </div>
        <div class="campo">
            <label>Peso kg</label>
            <input name="rows[{{ $i }}][weight_target]" class="input font-mono" inputmode="decimal" value="{{ isset($row['weight_target']) && $row['weight_target'] !== '' && $row['weight_target'] !== null ? kg($row['weight_target']) : '' }}" placeholder="0 = CL">
        </div>
        <div class="campo">
            <label>Recupero</label>
            <select name="rows[{{ $i }}][rest_seconds]" class="input font-mono">
                @foreach (config('gymlog.rest_options') as $seconds)
                    <option value="{{ $seconds }}" @selected((int) ($row['rest_seconds'] ?? 90) === $seconds)>{{ gmdate('i:s', $seconds) }}</option>
                @endforeach
            </select>
        </div>
        <div class="campo col-span-2 sm:col-span-5">
            <label>Note</label>
            <input name="rows[{{ $i }}][notes]" class="input" value="{{ $row['notes'] ?? '' }}" placeholder="es. sedile su 4, presa stretta" maxlength="255">
        </div>
    </div>
    <div class="flex flex-col gap-1 pt-6">
        <button type="button" class="btn btn-muto btn-piccolo !px-1.5" data-move="up" aria-label="Sposta su"><x-icon name="chevron-up" size="16" /></button>
        <button type="button" class="btn btn-muto btn-piccolo !px-1.5" data-move="down" aria-label="Sposta giù"><x-icon name="chevron-down" size="16" /></button>
        <button type="button" class="btn btn-muto btn-piccolo !px-1.5" data-remove aria-label="Togli esercizio"><x-icon name="x" size="16" /></button>
    </div>
</div>
