@extends('adminlte::page')

@section('template_title')
    Categorias Productos
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Categorias Productos') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('inventario.categorias-producto.create') }}"
                                    class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Create New') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        @include('inventario::partials.datatables-list-toolbar', [
                            'sortOptions' => [
                                'nombre_asc' => 'Nombre (A-Z)',
                                'nombre_desc' => 'Nombre (Z-A)',
                            ],
                            'defaultSort' => 'nombre_asc',
                        ])
                        <div class="table-responsive">
                            <table id="categoriasTable" class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoriasProductos as $categoriasProducto)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $categoriasProducto->nombre }}</td>

                                            <td>
                                                <form
                                                    action="{{ route('inventario.categorias-producto.destroy', $categoriasProducto->id_categoria) }}"
                                                    method="POST">
                                                    <a class="btn btn-sm btn-primary "
                                                        href="{{ route('inventario.categorias-producto.show', $categoriasProducto->id_categoria) }}"><i
                                                            class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('inventario.categorias-producto.edit', $categoriasProducto->id_categoria) }}"><i
                                                            class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i
                                                            class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">Usa los controles de la tabla para navegar entre páginas</small>
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
                defaultOrder: [[1, 'asc']],
                sortSelect: '#ordenarPor',
                sortMap: {
                    nombre_asc: [1, 'asc'],
                    nombre_desc: [1, 'desc'],
                },
            });
        });
    </script>
@endsection




