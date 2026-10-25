{{--
    IL DISCO: il badge di una scheda, disegnato come un disco del bilanciere visto di lato.
    Uso: <x-plate color="rosso" letter="A" />   size: sm | md | lg
--}}
@props(['color' => 'rosso', 'letter' => '', 'size' => 'md'])
@php
$sizes = ['sm' => 'w-6 h-9 text-sm', 'md' => 'w-8 h-12 text-lg', 'lg' => 'w-11 h-16 text-2xl'];
$bg = [
    'rosso' => 'bg-disco-rosso text-white', 'blu' => 'bg-disco-blu text-white', 'giallo' => 'bg-disco-giallo text-inchiostro',
    'verde' => 'bg-disco-verde text-white', 'bianco' => 'bg-disco-bianco text-inchiostro border border-inchiostro/30', 'nero' => 'bg-disco-nero text-white',
];
@endphp
<span {{ $attributes->merge(['class' => 'relative inline-grid place-items-center shrink-0 rounded-[3px] numero '.($sizes[$size] ?? $sizes['md']).' '.($bg[$color] ?? $bg['rosso'])]) }} aria-hidden="true">
    {{-- il "labbro" del disco: due righe sottili in alto e in basso --}}
    <span class="absolute inset-x-1 top-1 h-px bg-current opacity-25"></span>
    <span class="absolute inset-x-1 bottom-1 h-px bg-current opacity-25"></span>
    {{ $letter }}
</span>
