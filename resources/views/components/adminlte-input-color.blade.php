@props([
    'name',
    'label' => null,
    'value' => '#000000',
])

<div class="form-group">
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="color"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'form-control form-control-color']) }}
    >
</div>
