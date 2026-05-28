{{-- Encabezado de página del módulo inventario (una sola fila) --}}
@php
    $title = $title ?? '';
    $createRoute = $createRoute ?? null;
    $createLabel = $createLabel ?? 'Nuevo';
@endphp
<div class="row mb-3 align-items-center inventario-page-toolbar">
    <div class="col-sm-6">
        <h1 class="m-0">{{ $title }}</h1>
    </div>
    @if($createRoute)
    <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
        <a href="{{ $createRoute }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ $createLabel }}
        </a>
    </div>
    @endif
</div>
