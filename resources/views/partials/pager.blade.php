{{-- Paginazione minimale: ← Più recenti · pagina X di Y · Più vecchi → --}}
@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3" aria-label="Pagine">
        @if ($paginator->onFirstPage())<span></span>@else<a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm"><x-icon name="arrow-left" size="14" /> Più recenti</a>@endif
        <span class="text-[13px] text-fg-3">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm">Più vecchi <x-icon name="arrow-right" size="14" /></a>@else<span></span>@endif
    </nav>
@endif
