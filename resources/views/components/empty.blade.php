{{-- Stato vuoto: cosa manca e come rimediare. --}}
@props(['title', 'text' => null, 'href' => null, 'cta' => null])
<div class="flex flex-col items-start gap-1.5 py-5">
    <p class="text-fg" style="font-weight: 600">{{ $title }}</p>
    @if ($text)<p class="text-[14px] text-fg-3 max-w-prose">{{ $text }}</p>@endif
    @if ($href)<a href="{{ $href }}" class="btn btn-sm mt-2">{{ $cta }} <x-icon name="arrow-right" size="14" /></a>@endif
</div>
