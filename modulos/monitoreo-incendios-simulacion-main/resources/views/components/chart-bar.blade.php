@props(['chartId', 'labels' => [], 'datasets' => [], 'height' => 300, 'options' => []])

<div class="chart-container" style="position: relative; height:{{ $height }}px;">
    <canvas id="{{ $chartId }}" style="width:100%; height:100%; display:block;"
            data-chart-type="bar"
            data-chart-labels="{{ base64_encode(json_encode($labels)) }}"
            data-chart-datasets="{{ base64_encode(json_encode($datasets)) }}"
            data-chart-options="{{ base64_encode(json_encode($options)) }}"></canvas>
</div>
