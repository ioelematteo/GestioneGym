{{-- Card con titolo. Uso: <x-panel title="Pesi" subtitle="..."> ... </x-panel>  (slot "aside" a destra del titolo) --}}
@props(['title' => null, 'subtitle' => null])
<section {{ $attributes->merge(['class' => 'card p-4 sm:p-5 min-w-0']) }}>
    @if ($title || isset($aside))
        <header class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0">
                @if ($title)<h2 class="text-[16px] text-fg" style="font-weight: 620">{{ $title }}</h2>@endif
                @if ($subtitle)<p class="text-[13px] text-fg-3 mt-0.5">{{ $subtitle }}</p>@endif
            </div>
            {{ $aside ?? '' }}
        </header>
    @endif
    {{ $slot }}
</section>
