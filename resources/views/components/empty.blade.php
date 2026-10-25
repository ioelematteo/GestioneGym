{{-- Stato vuoto: cosa manca e come rimediare. --}}
@props(['title', 'text' => null, 'href' => null, 'cta' => null])
<div class="flex flex-col items-start gap-2 py-6">
    <p class="font-semibold">{{ $title }}</p>
    @if ($text)<p class="text-sm text-grafite max-w-prose">{{ $text }}</p>@endif
    @if ($href)<a href="{{ $href }}" class="btn btn-piccolo mt-1">{{ $cta }} <x-icon name="arrow-right" size="14" /></a>@endif
</div>
