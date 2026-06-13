@php
    $exportParams = request()->except('formato');
@endphp
<div class="btn-group btn-group-sm mr-2">
    <a href="{{ route($routeName, array_merge($exportParams, ['formato' => 'pdf'])) }}" class="btn btn-danger" target="_blank">
        <i class="fas fa-file-pdf"></i> PDF
    </a>
    <a href="{{ route($routeName, array_merge($exportParams, ['formato' => 'excel'])) }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Excel
    </a>
</div>
