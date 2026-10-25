{{-- Numero grande con etichetta. Uso: <x-stat label="Allenamenti" value="14" unit="kg"> piede </x-stat> --}}
@props(['label', 'value', 'unit' => null])
<div {{ $attributes->merge(['class' => 'pannello p-4 flex flex-col gap-2 min-w-0']) }}>
    <span class="etichetta">{{ $label }}</span>
    <span class="flex items-baseline gap-1">
        <span class="numero text-5xl">{{ $value }}</span>
        @if ($unit)<span class="text-sm text-grafite">{{ $unit }}</span>@endif
    </span>
    <div class="flex items-center justify-between gap-2 text-[12px] text-grafite min-h-6">{{ $slot }}</div>
</div>
