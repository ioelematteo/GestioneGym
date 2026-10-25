{{-- Bottone "Elimina" con conferma. Uso: <x-delete-button :action="route(...)" confirm="Sicuro?" /> --}}
@props(['action', 'confirm' => 'Sei sicuro?', 'label' => 'Elimina', 'icon' => false])
<form method="POST" action="{{ $action }}" data-confirm="{{ $confirm }}" class="inline">
    @csrf
    @method('DELETE')
    <button {{ $attributes->merge(['class' => $icon ? 'btn btn-muto btn-piccolo !px-2' : 'btn btn-pericolo btn-piccolo']) }} aria-label="{{ $label }}">
        <x-icon name="trash" size="15" />@unless ($icon) {{ $label }} @endunless
    </button>
</form>
