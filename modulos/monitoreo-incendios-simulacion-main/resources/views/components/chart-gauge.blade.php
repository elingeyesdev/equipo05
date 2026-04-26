@props(['chartId', 'value' => 0, 'max' => 100, 'label' => '', 'color' => '#3B82F6', 'height' => 420])

@php
    $percentage = ($max > 0) ? ($value / $max) * 100 : 0;
    
    // Determine color based on value
    $gaugeColor = $color;
    if ($percentage < 30) {
        $gaugeColor = '#10B981'; // Green - Low risk
    } elseif ($percentage < 60) {
        $gaugeColor = '#F59E0B'; // Yellow - Medium risk
    } else {
        $gaugeColor = '#EF4444'; // Red - High risk
    }
    
    // Make the remaining segment transparent so the main color fills most
    $datasets = [[
        'data' => [$value, max(0, $max - $value)],
        'backgroundColor' => [$gaugeColor, 'transparent'],
        'borderWidth' => 0,
    ]];

    // Gauge options: put semicircle configuration at chart-level so Chart.js
    // can compute proper aspect and avoid forcing excessive height.
    $gaugeOptions = [
        'plugins' => [
            'legend' => ['display' => false],
            'centerText' => [
                'value' => $value,
                'label' => $label,
                'color' => $gaugeColor
            ],
            'tooltip' => [
                'enabled' => false
            ]
        ],
        // Let the chart be responsive but do not maintain the default aspect ratio
        'responsive' => true,
        'maintainAspectRatio' => false,
        // Use a smaller aspect ratio so the semicircle doesn't become excessively tall
        // (reducing from 2 -> 1.4 gives a squatter gauge that fits better alongside other charts)
        'aspectRatio' => 1.4,
        // circumference expressed in degrees (use ~80% of full circle)
        'circumference' => 288,
        // rotation to center the arc vertically
        'rotation' => 216,
        // Cutout controls thickness of the arc; slightly larger cutout to reduce visual bulk
               'cutout' => '72%',
               // Make the donut thinner/wider by adjusting the cutout (can be overridden)
        // Ensure no borders on arc segments
        'elements' => [
            'arc' => [
                'borderWidth' => 0
            ]
        ]
    ];
@endphp

<div class="chart-container" style="position: relative; height:{{ $height }}px;">
    <canvas id="{{ $chartId }}" 
            data-chart-type="doughnut"
            data-chart-labels="{{ base64_encode(json_encode([$label, 'Restante'])) }}"
            data-chart-datasets="{{ base64_encode(json_encode($datasets)) }}"
            data-chart-options="{{ base64_encode(json_encode($gaugeOptions)) }}"></canvas>
</div>
