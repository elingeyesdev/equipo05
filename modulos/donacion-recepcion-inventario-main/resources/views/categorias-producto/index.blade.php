@extends('adminlte::page')

@section('template_title')
    Categorías de donación
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <span>
                                <i class="fas fa-layer-group mr-1"></i>
                                Categorías de productos donados
                            </span>
                            @if ($puedeGestionar ?? false)
                                <a href="{{ route('inventario.categorias-producto.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Registrar categoría
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        @include('inventario::partials.datatables-list-toolbar', [
                            'filters' => [
                                [
                                    'id' => 'filtroPrioridad',
                                    'label' => 'Prioridad',
                                    'placeholder' => 'Todas',
                                    'options' => ['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja'],
                                ],
                                [
                                    'id' => 'filtroTipo',
                                    'label' => 'Tipo',
                                    'placeholder' => 'Todos',
                                    'options' => \Modules\Inventario\Models\CategoriasProducto::TIPOS_CATEGORIA,
                                ],
                            ],
                            'sortOptions' => [
                                'prioridad_asc' => 'Prioridad (alta primero)',
                                'nombre_asc' => 'Nombre (A-Z)',
                                'codigo_asc' => 'Código (A-Z)',
                            ],
                            'defaultSort' => 'prioridad_asc',
                        ])

                        <div class="table-responsive">
                            <table id="categoriasTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Código</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Prioridad</th>
                                        <th>Perecedero</th>
                                        <th>Vencimiento</th>
                                        <th>Productos</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoriasProductos as $idx => $cat)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td><code>{{ $cat->codigo }}</code></td>
                                            <td>{{ $cat->nombre }}</td>
                                            <td>{{ \Modules\Inventario\Models\CategoriasProducto::TIPOS_CATEGORIA[$cat->tipo_categoria] ?? $cat->tipo_categoria }}</td>
                                            <td>
                                                @php $badge = match($cat->prioridad) { 'alta' => 'danger', 'media' => 'warning', default => 'secondary' }; @endphp
                                                <span class="badge badge-{{ $badge }}">{{ $cat->etiquetaPrioridad() }}</span>
                                            </td>
                                            <td>{{ $cat->es_perecedero ? 'Sí' : 'No' }}</td>
                                            <td>{{ $cat->requiere_fecha_vencimiento ? 'Sí' : 'No' }}</td>
                                            <td>{{ $cat->productos_count ?? 0 }}</td>
                                            <td class="text-nowrap">
                                                <a class="btn btn-sm btn-primary" href="{{ route('inventario.categorias-producto.show', $cat->id_categoria) }}" title="Ver"><i class="fa fa-eye"></i></a>
                                                @if ($puedeGestionar ?? false)
                                                    <a class="btn btn-sm btn-success" href="{{ route('inventario.categorias-producto.edit', $cat->id_categoria) }}" title="Editar"><i class="fa fa-edit"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')
    @include('inventario::partials.datatables-inventario-init')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            initInventarioListTable({
                selector: '#categoriasTable',
                defaultOrder: [[4, 'asc']],
                sortSelect: '#ordenarPor',
                sortMap: {
                    prioridad_asc: [[4, 'asc'], [2, 'asc']],
                    nombre_asc: [2, 'asc'],
                    codigo_asc: [1, 'asc'],
                },
                filters: [
                    { select: '#filtroPrioridad', column: 4 },
                    { select: '#filtroTipo', column: 3 },
                ],
            });
        });
    </script>
@endsection
