@props([
    'theme' => 'info',
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'border-left border-3 p-3 mb-3 bg-light']) }} style="border-color: var(--{{ $theme }}, #17a2b8);">
    @if($title)
        <h5 class="mb-2">{{ $title }}</h5>
    @endif
    {{ $slot }}
</div>
