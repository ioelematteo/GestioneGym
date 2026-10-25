{{--
    BADGE DI UNA SCHEDA: la lettera ("A", "B") nel colore della scheda.
    Uso: <x-badge color="blu" letter="A" />   size: sm | md | lg
--}}
@props(['color' => 'verde', 'letter' => '', 'size' => 'md'])
@php
$sizes = ['sm' => 'size-8 text-[13px] rounded-[10px]', 'md' => 'size-10 text-[15px] rounded-xl', 'lg' => 'size-12 text-lg rounded-[14px]'];
@endphp
<span {{ $attributes->merge(['class' => 'num inline-grid place-items-center shrink-0 '.($sizes[$size] ?? $sizes['md'])]) }}
      style="--c: var(--color-w-{{ $color }}, var(--color-accent)); color: var(--c); background: color-mix(in oklab, var(--c) 15%, transparent); box-shadow: inset 0 0 0 1px color-mix(in oklab, var(--c) 28%, transparent)"
      aria-hidden="true">{{ $letter }}</span>
