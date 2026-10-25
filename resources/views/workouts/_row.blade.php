{{-- Una riga esercizio nel form scheda. $i = indice (o __I__ nel modello), $row = dati --}}
<div class="py-4 flex flex-col gap-3" data-row>
    <div class="flex items-end gap-2">
        <div class="field flex-1">
            <label>Esercizio</label>
            <input name="rows[{{ $i }}][name]" class="input" list="catalog" value="{{ $row['name'] ?? '' }}" placeholder="es. Panca inclinata" autocomplete="off" data-row-name>
        </div>
        <div class="flex">
            <button type="button" class="btn btn-ghost btn-icon" data-move="up" aria-label="Sposta su"><x-icon name="chevron-up" size="18" /></button>
            <button type="button" class="btn btn-ghost btn-icon" data-move="down" aria-label="Sposta giù"><x-icon name="chevron-down" size="18" /></button>
            <button type="button" class="btn btn-ghost btn-icon text-fg-3" data-remove aria-label="Togli esercizio"><x-icon name="x" size="18" /></button>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-2">
        <div class="field">
            <label>Serie</label>
            <input name="rows[{{ $i }}][sets]" inputmode="numeric" class="input num !px-2 text-center" value="{{ $row['sets'] ?? 3 }}">
        </div>
        <div class="field">
            <label>Rep</label>
            <input name="rows[{{ $i }}][reps_target]" class="input num !px-2 text-center" value="{{ $row['reps_target'] ?? '' }}" placeholder="8-10" maxlength="20">
        </div>
        <div class="field">
            <label>Kg</label>
            <input name="rows[{{ $i }}][weight_target]" class="input num !px-2 text-center" inputmode="decimal" value="{{ isset($row['weight_target']) && $row['weight_target'] !== '' && $row['weight_target'] !== null ? kg($row['weight_target']) : '' }}" placeholder="0">
        </div>
        <div class="field">
            <label>Recupero</label>
            <select name="rows[{{ $i }}][rest_seconds]" class="input num !px-2 !pr-6 text-center !bg-[right_6px_center]">
                @foreach (config('gymlog.rest_options') as $seconds)
                    <option value="{{ $seconds }}" @selected((int) ($row['rest_seconds'] ?? 90) === $seconds)>{{ $seconds < 60 ? $seconds.'s' : rtrim(rtrim(number_format($seconds / 60, 1, '.', ''), '0'), '.').'′' }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <input name="rows[{{ $i }}][notes]" class="input !h-10 !text-[14px]" value="{{ $row['notes'] ?? '' }}" placeholder="Note (es. sedile su 4)" maxlength="255" aria-label="Note">
</div>
