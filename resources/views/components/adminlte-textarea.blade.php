@props([
    'name',
    'label' => null,
    'rows' => 3,
    'placeholder' => null,
    'value' => null,
])

<div class="form-group">
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    >{{ old($name, $value) }}</textarea>
</div>
