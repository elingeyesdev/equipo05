<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['chartId', 'value' => 0, 'max' => 100, 'label' => '', 'color' => '#3B82F6', 'height' => 420]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['chartId', 'value' => 0, 'max' => 100, 'label' => '', 'color' => '#3B82F6', 'height' => 420]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<div class="chart-container" style="position: relative; height:<?php echo e($height); ?>px;">
    <canvas id="<?php echo e($chartId); ?>" 
            data-chart-type="doughnut"
            data-chart-labels="<?php echo e(base64_encode(json_encode([$label, 'Restante']))); ?>"
            data-chart-datasets="<?php echo e(base64_encode(json_encode($datasets))); ?>"
            data-chart-options="<?php echo e(base64_encode(json_encode($gaugeOptions))); ?>"></canvas>
</div>
<?php /**PATH C:\Users\lenovo\OneDrive\Desktop\Proyectos\SIPII Laravel\Laraprueba-CRUD\Laraprueba-CRUD\resources\views/components/chart-gauge.blade.php ENDPATH**/ ?>