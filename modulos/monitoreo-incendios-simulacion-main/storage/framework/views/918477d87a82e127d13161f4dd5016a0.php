<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['chartId', 'labels' => [], 'datasets' => [], 'type' => 'line', 'height' => 300, 'options' => null]));

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

foreach (array_filter((['chartId', 'labels' => [], 'datasets' => [], 'type' => 'line', 'height' => 300, 'options' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<div class="chart-container" style="position: relative; height:<?php echo e($height); ?>px;">
    <canvas id="<?php echo e($chartId); ?>" 
            data-chart-type="<?php echo e($type); ?>"
            data-chart-labels="<?php echo e(base64_encode(json_encode($labels, JSON_NUMERIC_CHECK))); ?>"
            data-chart-datasets="<?php echo e(base64_encode(json_encode($datasets, JSON_NUMERIC_CHECK))); ?>"
            data-chart-options="<?php echo e(base64_encode(json_encode($options, JSON_FORCE_OBJECT))); ?>"></canvas>
</div>
<?php /**PATH C:\Users\lenovo\OneDrive\Desktop\Proyectos\SIPII Laravel\Laraprueba-CRUD\Laraprueba-CRUD\resources\views/components/chart-line.blade.php ENDPATH**/ ?>