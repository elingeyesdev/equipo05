@props([
    'theme' => 'info',
    'title' => null,
    'dismissable' => false,
])

<div {{ $attributes->merge(['class' => 'alert alert-' . $theme]) }} role="alert">
    @if($dismissable)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
    @if($title)
        <strong>{{ $title }}</strong>
    @endif
    {{ $slot }}
</div>
