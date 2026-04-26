<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['chartId', 'labels' => [], 'data' => [], 'colors' => [], 'height' => 300, 'options' => []]));

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

foreach (array_filter((['chartId', 'labels' => [], 'data' => [], 'colors' => [], 'height' => 300, 'options' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'];
    $backgroundColors = !empty($colors) ? $colors : $defaultColors;
    
    $datasets = [[
        'data' => $data,
        'backgroundColor' => $backgroundColors,
        'borderWidth' => 2,
        'borderColor' => '#fff'
    ]];
?>

<div class="chart-container" style="position: relative; height:<?php echo e($height); ?>px;">
    <canvas id="<?php echo e($chartId); ?>" 
            data-chart-type="pie"
            data-chart-labels="<?php echo e(base64_encode(json_encode($labels))); ?>"
            data-chart-datasets="<?php echo e(base64_encode(json_encode($datasets))); ?>"
            data-chart-options="<?php echo e(base64_encode(json_encode(array_merge(['plugins' => ['legend' => ['position' => 'right']]], $options)))); ?>"></canvas>
</div>
<?php /**PATH C:\Users\lenovo\OneDrive\Desktop\Proyectos\SIPII Laravel\Laraprueba-CRUD\Laraprueba-CRUD\resources\views/components/chart-pie.blade.php ENDPATH**/ ?>