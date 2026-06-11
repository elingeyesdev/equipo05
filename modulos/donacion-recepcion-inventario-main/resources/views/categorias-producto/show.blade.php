@extends('adminlte::page')

@section('template_title')
    {{ $categoriasProducto->nombre }}
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <span class="card-title">
                            {{ $categoriasProducto->nombre }}
                            <code class="ml-2">{{ $categoriasProducto->codigo }}</code>
                        </span>
                        <div>
                            @if ($puedeGestionar ?? false)
                                <a class="btn btn-success btn-sm" href="{{ route('inventario.categorias-producto.edit', $categoriasProducto->id_categoria) }}">Editar</a>
                            @endif
                            <a class="btn btn-primary btn-sm" href="{{ route('inventario.categorias-producto.index') }}">Volver</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-muted">Clasificación</h5>
                                <p><strong>Tipo:</strong> {{ \Modules\Inventario\Models\CategoriasProducto::TIPOS_CATEGORIA[$categoriasProducto->tipo_categoria] ?? $categoriasProducto->tipo_categoria }}</p>
                                <p><strong>Prioridad en emergencia:</strong> {{ $categoriasProducto->etiquetaPrioridad() }}</p>
                                <p><strong>Unidad por defecto:</strong> {{ $categoriasProducto->unidad_medida ?: '—' }}</p>
                                <p><strong>Perecedero:</strong> {{ $categoriasProducto->es_perecedero ? 'Sí' : 'No' }}</p>
                                <p><strong>Requiere vencimiento:</strong> {{ $categoriasProducto->requiere_fecha_vencimiento ? 'Sí' : 'No' }}</p>
                                <p><strong>Productos asociados:</strong> {{ $categoriasProducto->productos_count ?? 0 }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-muted">Manejo en emergencia</h5>
                                <p><strong>Descripción:</strong><br>{{ $categoriasProducto->descripcion ?: '—' }}</p>
                                <p><strong>Almacenamiento:</strong><br>{{ $categoriasProducto->condiciones_almacenamiento ?: '—' }}</p>
                                <p><strong>Recomendaciones de uso:</strong><br>{{ $categoriasProducto->recomendaciones_uso ?: '—' }}</p>
                            </div>
                        </div>

                        @if ($categoriasProducto->historial->isNotEmpty())
                            <hr>
                            <h5><i class="fas fa-history"></i> Historial de cambios</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Acción</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categoriasProducto->historial as $h)
                                            <tr>
                                                <td>{{ $h->created_at?->format('d/m/Y H:i') }}</td>
                                                <td>{{ ucfirst($h->accion) }}</td>
                                                <td>{{ $h->usuario_ci ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
