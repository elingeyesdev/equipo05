@props([
    'title' => null,
    'text' => null,
    'icon' => null,
    'theme' => 'info',
])

<div {{ $attributes->merge(['class' => 'small-box bg-' . $theme]) }}>
    <div class="inner">
        @if($title)<h3>{{ $title }}</h3>@endif
        @if($text)<p>{{ $text }}</p>@endif
        {{ $slot }}
    </div>
    @if($icon)
        <div class="icon"><i class="{{ $icon }}"></i></div>
    @endif
</div>
