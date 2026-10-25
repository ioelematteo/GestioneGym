{{-- Variazione: ↑ +3 / ↓ −1.2 kg. neutral = grigio (es. peso corporeo: su o giù dipende dall'obiettivo) --}}
@props(['delta' => null, 'unit' => '', 'neutral' => false])
@if ($delta !== null)
    @php
    $up = $delta > 0;
    $zero = (float) $delta == 0.0;
    $tone = $neutral || $zero ? 'text-fg-2 bg-surface-2' : ($up ? 'text-accent bg-accent/10' : 'text-danger bg-danger/10');
    $text = ($zero ? '±' : ($up ? '+' : '−')).rtrim(rtrim(number_format(abs($delta), 1, '.', ''), '0'), '.').$unit;
    @endphp
    <span {{ $attributes->merge(['class' => "num inline-flex items-center gap-0.5 h-6 px-2 rounded-full text-[12px] whitespace-nowrap $tone"]) }}>
        @unless ($zero)<x-icon :name="$up ? 'up' : 'down'" size="12" />@endunless{{ $text }}
    </span>
@endif
