{{-- Bottone "Elimina" con conferma. Uso: <x-delete-button :action="route(...)" confirm="Sicuro?" /> (icon = solo icona) --}}
@props(['action', 'confirm' => 'Sei sicuro?', 'label' => 'Elimina', 'icon' => false])
<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm }}" class="inline-flex">
    @csrf
    @method('DELETE')
    <button {{ $attributes->merge(['class' => $icon ? 'btn btn-ghost btn-sm btn-icon' : 'btn btn-danger btn-sm']) }} aria-label="{{ $label }}">
        <x-icon name="trash" size="16" />@unless ($icon) {{ $label }} @endunless
    </button>
</form>
