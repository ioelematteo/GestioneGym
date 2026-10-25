{{-- Campo di un form con etichetta ed errore. Uso: <x-field label="Peso" name="weight_kg"> <input ...> </x-field> --}}
@props(['label', 'name' => null, 'hint' => null])
<div {{ $attributes->merge(['class' => 'campo']) }}>
    <label @if ($name) for="{{ $name }}" @endif>{{ $label }}</label>
    {{ $slot }}
    @if ($hint)<p class="text-[12px] text-polvere">{{ $hint }}</p>@endif
    @if ($name)
        @error($name)<p class="errore">{{ $message }}</p>@enderror
    @endif
</div>
