@php
    $src = rescate_media_url($path ?? null, $seed ?? 'fauna');
    $altText = $alt ?? 'Fauna silvestre';
    $imgClass = trim('res-entity-card-img '.($class ?? ''));
@endphp
<div class="res-entity-card-img-wrap">
    <img src="{{ $src }}" alt="{{ $altText }}" class="{{ $imgClass }}" loading="lazy">
    @if(!empty($badge))
        <span class="res-species-badge">{{ $badge }}</span>
    @endif
</div>
