{{-- Variazione: ↑ +3 / ↓ −1.2 kg. neutral = grigio (es. peso corporeo: su o giù dipende dall'obiettivo) --}}
@props(['delta' => null, 'unit' => '', 'neutral' => false])
@if ($delta !== null)
    @php
    $up = $delta > 0; $zero = (float) $delta == 0.0;
    $tone = $neutral || $zero ? 'text-grafite bg-cemento' : ($up ? 'text-disco-verde bg-disco-verde/10' : 'text-disco-rosso bg-disco-rosso/10');
    $text = ($zero ? '±' : ($up ? '+' : '−')).rtrim(rtrim(number_format(abs($delta), 1, '.', ''), '0'), '.').$unit;
    @endphp
    <span {{ $attributes->merge(['class' => "inline-flex items-center gap-0.5 font-mono text-[12px] px-1.5 py-0.5 rounded-sm $tone"]) }}>
        @unless ($zero)<x-icon :name="$up ? 'up' : 'down'" size="12" />@endunless{{ $text }}
    </span>
@endif
