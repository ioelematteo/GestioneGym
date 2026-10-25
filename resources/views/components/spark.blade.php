{{-- Mini grafico a linea in SVG (niente JS). Uso: <x-spark :values="[54.1, 54.6, 55.2]" /> --}}
@props(['values' => [], 'width' => 88, 'height' => 28])
@php
$values = array_values($values);
$n = count($values);
@endphp
@if ($n >= 2)
    @php
    $min = min($values);
    $range = (max($values) - $min) ?: 1;
    $points = [];
    foreach ($values as $i => $v) {
        $points[] = round($i * $width / ($n - 1), 1).','.round($height - 3 - ($v - $min) / $range * ($height - 6), 1);
    }
    [$lx, $ly] = explode(',', end($points));
    $line = 'M'.implode(' L', $points);
    @endphp
    <svg {{ $attributes->merge(['class' => 'shrink-0 overflow-visible text-accent']) }} width="{{ $width }}" height="{{ $height }}" viewBox="0 0 {{ $width }} {{ $height }}" aria-hidden="true">
        <path d="{{ $line }} L{{ $width }},{{ $height }} L0,{{ $height }} Z" fill="currentColor" opacity="0.1" />
        <path d="{{ $line }}" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" stroke-linecap="round" />
        <circle cx="{{ $lx }}" cy="{{ $ly }}" r="2.75" fill="currentColor" />
    </svg>
@endif
