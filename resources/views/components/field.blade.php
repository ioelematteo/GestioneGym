{{-- Campo di un form con etichetta ed errore. Uso: <x-field label="Peso" name="weight_kg"> <input class="input" ...> </x-field> --}}
@props(['label', 'name' => null, 'hint' => null])
<div {{ $attributes->merge(['class' => 'field']) }}>
    <label @if ($name) for="{{ $name }}" @endif>{{ $label }}</label>
    {{ $slot }}
    @if ($hint)<p class="text-[12px] text-fg-3">{{ $hint }}</p>@endif
    @if ($name)
        @error($name)<p class="error">{{ $message }}</p>@enderror
    @endif
</div>
