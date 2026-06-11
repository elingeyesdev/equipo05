@extends('adminlte::page')

@section('template_title')
    {{ $producto->nombre }}
@endsection

@section('content')
@include('inventario::partials.flash-messages')
<section class="content container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span class="card-title">
                {{ $producto->nombre }}
                <code class="ml-2">{{ $producto->codigo }}</code>
                <span class="badge badge-{{ $producto->badgeEstado() }} ml-1">{{ $producto->etiquetaEstado() }}</span>
                <span class="badge badge-{{ $producto->badgePrioridad() }} ml-1">{{ $producto->etiquetaPrioridad() }}</span>
            </span>
            <div>
                <a class="btn btn-warning btn-sm" href="{{ route('inventario.producto.edit', $producto->id_producto) }}">Editar</a>
                <a class="btn btn-primary btn-sm" href="{{ route('inventario.producto.index') }}">Volver</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-muted">Identificación</h5>
                    <p><strong>Categoría:</strong> {{ $producto->categoriaProducto->nombre ?? 'Sin categoría' }}</p>
                    <p><strong>Descripción:</strong> {{ $producto->descripcion ?: '—' }}</p>
                    <p><strong>Unidad de medida:</strong> {{ $producto->unidad_medida ?: '—' }}</p>
                    <p><strong>Stock mínimo (alerta):</strong> {{ $producto->stock_minimo ?? 0 }}</p>
                    @if ($producto->imagen_url)
                        <p><strong>Imagen:</strong> <a href="{{ $producto->imagen_url }}" target="_blank" rel="noopener">Ver imagen</a></p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5 class="text-muted">Control operativo</h5>
                    <p><strong>Requiere vencimiento:</strong> {{ $producto->requiere_vencimiento ? 'Sí' : 'No' }}</p>
                    <p><strong>Requiere talla:</strong> {{ $producto->requiere_talla ? 'Sí' : 'No' }}</p>
                    <p><strong>Requiere condición:</strong> {{ $producto->requiere_condicion ? 'Sí' : 'No' }}</p>
                    <p><strong>Producto restringido:</strong> {{ $producto->producto_restringido ? 'Sí' : 'No' }}</p>
                    <p><strong>Registros en donaciones:</strong> {{ $producto->donacion_detalles_count ?? 0 }}</p>
                </div>
                <div class="col-md-12">
                    <p><strong>Condiciones de almacenamiento:</strong><br>{{ $producto->condiciones_almacenamiento ?: '—' }}</p>
                    <p><strong>Observaciones:</strong><br>{{ $producto->observaciones ?: '—' }}</p>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                Este registro es el <strong>catálogo base</strong>. El stock físico se gestiona en donaciones, detalles y movimientos de inventario.
            </div>
        </div>
    </div>
</section>
@endsection
