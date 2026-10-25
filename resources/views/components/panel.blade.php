{{-- Un pannello con titolo. Uso: <x-panel title="Frequenza" subtitle="..."> ... </x-panel>, slot "aside" a destra --}}
@props(['title' => null, 'subtitle' => null])
<section {{ $attributes->merge(['class' => 'pannello p-4 sm:p-5 min-w-0']) }}>
    @if ($title || isset($aside))
        <header class="flex items-start justify-between gap-3 mb-4">
            <div class="min-w-0">
                @if ($title)<h2 class="font-semibold text-[15px]">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="text-[13px] text-grafite mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            {{ $aside ?? '' }}
        </header>
    @endif
    {{ $slot }}
</section>
