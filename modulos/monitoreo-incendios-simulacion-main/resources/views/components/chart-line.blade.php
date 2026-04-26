@props(['chartId', 'labels' => [], 'datasets' => [], 'type' => 'line', 'height' => 300, 'options' => null])

@php
    // Debug: verificar datos antes de codificar
    if (config('app.debug')) {
        \Log::info("Chart {$chartId} datos:", [
            'labels' => $labels,
            'labels_type' => gettype($labels),
            'labels_count' => is_array($labels) ? count($labels) : 'no es array',
            'datasets' => $datasets,
            'datasets_type' => gettype($datasets),
            'datasets_count' => is_array($datasets) ? count($datasets) : 'no es array'
        ]);
    }
    
    // Asegurar que labels y datasets son arrays
    $labels = is_array($labels) ? array_values($labels) : [];
    $datasets = is_array($datasets) ? array_values($datasets) : [];
    
    // Asegurar que options sea un objeto, no un array
    $options = is_array($options) && empty($options) ? new \stdClass() : ($options ?? new \stdClass());
@endphp

<div class="chart-container" style="position: relative; height:{{ $height }}px;">
    <canvas id="{{ $chartId }}" 
            data-chart-type="{{ $type }}"
            data-chart-labels="{{ base64_encode(json_encode($labels, JSON_NUMERIC_CHECK)) }}"
            data-chart-datasets="{{ base64_encode(json_encode($datasets, JSON_NUMERIC_CHECK)) }}"
            data-chart-options="{{ base64_encode(json_encode($options, JSON_FORCE_OBJECT)) }}"></canvas>
</div>
