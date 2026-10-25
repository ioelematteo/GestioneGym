{{--
    UNA SERIE: peso (di QUESTA serie), rep, ✓  — sotto: l'ultima volta (tocca per copiare) + stato.
    Variabili: $exerciseId, $n, $last (SessionSet|null), $target (peso della scheda|null), $states
    Nomi dei campi: sets[ESERCIZIO][NUMERO][weight|reps|state]
--}}
@php
$field = "sets[{$exerciseId}][{$n}]";
$old = "sets.{$exerciseId}.{$n}";
$ghostWeight = $last ? kg($last->weight) : ($target !== null ? kg($target) : 'kg');
@endphp
<div class="set grid grid-cols-[20px_1fr_1fr_56px] items-center gap-x-2 gap-y-2 py-3" data-set>
    <span class="num text-[13px] text-fg-3 text-center" data-set-number>{{ $n }}</span>

    <input class="input num !h-14 !text-[22px] text-center !px-2" name="{{ $field }}[weight]" value="{{ old($old.'.weight') }}"
           inputmode="decimal" enterkeyhint="next" autocomplete="off" placeholder="{{ $ghostWeight }}" aria-label="Kg serie {{ $n }}" data-weight>

    <input class="input num !h-14 !text-[22px] text-center !px-2" name="{{ $field }}[reps]" value="{{ old($old.'.reps') }}"
           inputmode="numeric" enterkeyhint="done" autocomplete="off" placeholder="{{ $last?->reps ?? 'rep' }}" aria-label="Rep serie {{ $n }}" data-reps>

    <button type="button" class="grid place-items-center size-14 rounded-2xl border border-line-strong bg-surface-2 text-fg-3 transition active:scale-95
                                aria-pressed:bg-accent aria-pressed:border-accent aria-pressed:text-accent-ink cursor-pointer"
            aria-pressed="false" aria-label="Serie {{ $n }} fatta" data-done>
        <x-icon name="check" size="22" />
    </button>

    <div class="col-start-2 col-span-3 flex items-center gap-2 min-w-0">
        @if ($last)
            <button type="button" class="flex-1 min-w-0 flex items-center gap-1.5 text-left text-[12px] text-fg-3 hover:text-fg cursor-pointer"
                    title="Tocca per copiare" data-copy data-last-weight="{{ kg($last->weight) }}" data-last-reps="{{ $last->reps }}">
                <x-icon name="repeat" size="13" />
                <span class="truncate">ultima <b class="num text-fg-2" style="font-weight: 600">{{ serie($last->weight, $last->reps) }}</b>@if ($last->state) · {{ $last->state->name }}@endif</span>
            </button>
        @else
            <span class="flex-1 truncate text-[12px] text-fg-3">{{ $target !== null ? 'prima volta · previsti '.kg($target).' kg' : 'prima volta' }}</span>
        @endif

        <select class="input !h-9 !w-[8.5rem] !rounded-full !text-[13px] !pl-3 !pr-8 text-fg-2 shrink-0" name="{{ $field }}[state]" aria-label="Stato serie {{ $n }}">
            <option value="">Pulita</option>
            @foreach ($states as $state)
                <option value="{{ $state->id }}" @selected(old($old.'.state') == $state->id)>{{ $state->name }}</option>
            @endforeach
        </select>
    </div>
</div>
