{{-- Numero con etichetta. Uso: <x-stat label="Allenamenti" value="14" unit="kg"> riga sotto </x-stat> (slot "chart" opzionale) --}}
@props(['label', 'value', 'unit' => null])
<div {{ $attributes->merge(['class' => 'card p-4 flex flex-col gap-1 min-w-0']) }}>
    <span class="text-[13px] text-fg-3">{{ $label }}</span>
    <span class="flex items-baseline gap-1 min-w-0">
        <span class="num text-[28px] sm:text-[32px] leading-none text-fg truncate">{{ $value }}</span>
        @if ($unit)<span class="text-[13px] text-fg-3">{{ $unit }}</span>@endif
    </span>
    <span class="flex items-center justify-between gap-2 mt-2 min-h-6 text-[12px] text-fg-3">{{ $slot }}</span>
</div>
