{{-- Icona SVG a linea. Uso: <x-icon name="home" /> o <x-icon name="check" size="16" /> --}}
@props(['name', 'size' => 20])
@php
$paths = [
    'home' => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
    'barbell' => '<path d="M6.5 6v12M3.5 9v6M17.5 6v12M20.5 9v6M6.5 12h11"/>',
    'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'body' => '<circle cx="12" cy="4.5" r="2"/><path d="M5 9h14M12 9v6M9 21l3-6 3 6"/>',
    'coach' => '<path d="M4 5h16v11H9l-5 4z"/><path d="M8 9h8M8 12h5"/>',
    'history' => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'check' => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
    'x' => '<path d="M6 6l12 12M18 6 6 18"/>',
    'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
    'up' => '<path d="M12 19V5M6 11l6-6 6 6"/>',
    'down' => '<path d="M12 5v14M6 13l6 6 6-6"/>',
    'chevron-up' => '<path d="m6 15 6-6 6 6"/>',
    'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
    'play' => '<path d="M8 5.5v13l11-6.5z" fill="currentColor"/>',
    'edit' => '<path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16z"/>',
    'trash' => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
    'repeat' => '<path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/>',
    'timer' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2M9 2h6"/>',
    'camera' => '<path d="M4 7h3l2-3h6l2 3h3v13H4z"/><circle cx="12" cy="13" r="3.5"/>',
    'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0 1 14 0M16 4.5a3.5 3.5 0 0 1 0 7M18 20a7 7 0 0 0-2-5"/>',
    'lock' => '<rect x="4" y="11" width="16" height="10" rx="1"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    'logout' => '<path d="M9 21H4V3h5M16 17l5-5-5-5M21 12H9"/>',
    'note' => '<path d="M5 3h14v18H5z"/><path d="M9 8h6M9 12h6M9 16h3"/>',
    'list' => '<path d="M8 6h13M8 12h13M8 18h13M3 6h1M3 12h1M3 18h1"/>',
];
@endphp
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="square" stroke-linejoin="miter" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
