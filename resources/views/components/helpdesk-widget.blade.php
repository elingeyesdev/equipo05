@props([
    'width' => config('helpdeskwidget.iframe_width', '100%'),
    'height' => config('helpdeskwidget.iframe_height', '600px'),
    'src' => null,
])

@php
    $widgetSrc = $src ?: config('helpdeskwidget.api_url', 'https://helpdesk.example.com');
@endphp

<iframe
    src="{{ $widgetSrc }}"
    width="{{ $width }}"
    height="{{ $height }}"
    style="border:0;"
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"
    title="Helpdesk Widget"
></iframe>
