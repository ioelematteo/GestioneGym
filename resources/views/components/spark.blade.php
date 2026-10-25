{{-- Mini grafico a linea in SVG (niente JS). Uso: <x-spark :values="[54.1, 54.6, 55.2]" /> --}}
@props(['values' => [], 'width' => 96, 'height' => 28])
@php
$values = array_values($values);
$n = count($values);
@endphp
@if ($n >= 2)
    @php
    $min = min($values); $range = (max($values) - $min) ?: 1;
    $points = [];
    foreach ($values as $i => $v) {
        $points[] = round($i * $width / ($n - 1), 1).','.round($height - 3 - ($v - $min) / $range * ($height - 6), 1);
    }
    [$lx, $ly] = explode(',', end($points));
    @endphp
    <svg {{ $attributes->merge(['class' => 'shrink-0 overflow-visible']) }} width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" aria-hidden="true">
        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
        <circle cx="{{ $lx }}" cy="{{ $ly }}" r="3" fill="var(--color-disco-rosso)" />
    </svg>
@endif
