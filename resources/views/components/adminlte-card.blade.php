@props([
    'title' => null,
    'theme' => 'primary',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'card card-' . $theme]) }}>
    @if($title)
        <div class="card-header">
            <h3 class="card-title">
                @if($icon)<i class="{{ $icon }} mr-1"></i>@endif
                {{ $title }}
            </h3>
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
