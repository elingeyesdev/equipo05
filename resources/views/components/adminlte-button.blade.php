@props([
    'label' => null,
    'theme' => 'primary',
    'icon' => null,
    'type' => 'button',
    'disabled' => false,
])

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'btn btn-' . $theme]) }}>
    @if($icon)<i class="{{ $icon }} mr-1"></i>@endif
    {{ $label ?? $slot }}
</button>
