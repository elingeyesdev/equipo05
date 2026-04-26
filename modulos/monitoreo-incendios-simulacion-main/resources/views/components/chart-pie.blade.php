@props(['chartId', 'labels' => [], 'data' => [], 'colors' => [], 'height' => 300, 'options' => []])

@php
    $defaultColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'];
    $backgroundColors = !empty($colors) ? $colors : $defaultColors;
    
    $datasets = [[
        'data' => $data,
        'backgroundColor' => $backgroundColors,
        'borderWidth' => 2,
        'borderColor' => '#fff'
    ]];
@endphp

<div class="chart-container" style="position: relative; height:{{ $height }}px;">
    <canvas id="{{ $chartId }}" 
            data-chart-type="pie"
            data-chart-labels="{{ base64_encode(json_encode($labels)) }}"
            data-chart-datasets="{{ base64_encode(json_encode($datasets)) }}"
            data-chart-options="{{ base64_encode(json_encode(array_merge(['plugins' => ['legend' => ['position' => 'right']]], $options))) }}"></canvas>
</div>
