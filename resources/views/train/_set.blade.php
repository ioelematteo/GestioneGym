{{--
    UNA SERIE: peso (di QUESTA serie), rep, ✓, e sotto "ultima volta" + stato.
    Variabili: $exerciseId, $n, $last (SessionSet|null), $target (peso della scheda|null), $states
    Nomi dei campi: sets[ESERCIZIO][NUMERO][weight|reps|state]
--}}
@php
$field = "sets[{$exerciseId}][{$n}]";
$old = "sets.{$exerciseId}.{$n}";
$ghostWeight = $last ? kg($last->weight) : ($target !== null ? kg($target) : 'kg');
@endphp
<div class="grid grid-cols-[22px_1fr_1fr_48px] items-center gap-x-2 gap-y-1.5 py-3 border-t border-riga" data-set>
    <span class="font-mono text-xs text-polvere text-center" data-set-number>{{ $n }}</span>

    <input class="input numero !text-2xl !h-12 text-center placeholder:!text-polvere/45" name="{{ $field }}[weight]" value="{{ old($old.'.weight') }}"
           inputmode="decimal" autocomplete="off" placeholder="{{ $ghostWeight }}" aria-label="Kg serie {{ $n }}" data-weight>

    <input class="input numero !text-2xl !h-12 text-center placeholder:!text-polvere/45" name="{{ $field }}[reps]" value="{{ old($old.'.reps') }}"
           inputmode="numeric" autocomplete="off" placeholder="{{ $last?->reps ?? 'rep' }}" aria-label="Rep serie {{ $n }}" data-reps>

    <button type="button" class="grid place-items-center h-12 rounded-sm border border-inchiostro/25 text-polvere hover:text-inchiostro aria-pressed:bg-inchiostro aria-pressed:text-white aria-pressed:border-inchiostro cursor-pointer"
            aria-pressed="false" aria-label="Serie {{ $n }} fatta" data-done>
        <x-icon name="check" size="20" />
    </button>

    <div class="col-start-2 col-span-3 flex items-center gap-2 min-w-0">
        @if ($last)
            <button type="button" class="flex-1 min-w-0 flex items-center gap-1.5 text-left text-[12px] text-polvere hover:text-inchiostro cursor-pointer" title="Tocca per copiare"
                    data-copy data-last-weight="{{ kg($last->weight) }}" data-last-reps="{{ $last->reps }}">
                <x-icon name="repeat" size="12" />
                <span class="truncate">ultima <b class="font-mono font-medium text-grafite">{{ serie($last->weight, $last->reps) }}</b>@if ($last->state) · {{ $last->state->name }}@endif</span>
            </button>
        @else
            <span class="flex-1 text-[12px] text-polvere italic">{{ $target !== null ? 'prima volta · previsto '.kg($target).' kg' : 'prima volta' }}</span>
        @endif

        <select class="input !h-8 !w-36 !text-[13px] text-grafite" name="{{ $field }}[state]" aria-label="Stato serie {{ $n }}">
            <option value="">Pulita</option>
            @foreach ($states as $state)
                <option value="{{ $state->id }}" @selected(old($old.'.state') == $state->id)>{{ $state->name }}</option>
            @endforeach
        </select>
    </div>
</div>
