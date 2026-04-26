@props([
    'name',
    'label' => null,
    'value' => 0,
    'min' => 0,
    'max' => 100,
    'step' => 1,
])

<div class="form-group">
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="range"
        value="{{ old($name, $value) }}"
        min="{{ $min }}"
        max="{{ $max }}"
        step="{{ $step }}"
        {{ $attributes->merge(['class' => 'form-control-range']) }}
    >
</div>
