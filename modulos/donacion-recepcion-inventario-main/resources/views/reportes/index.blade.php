@extends('adminlte::page')

@section('title', 'Reportes — Inventario')
@section('subtitle', 'Generación de informes de donaciones, inventario y logística.')

@section('content_header')
<h1 class="m-0"><i class="fas fa-chart-bar text-primary"></i> Centro de reportes</h1>
@endsection

@section('content')
@include('inventario::partials.flash-messages')
<div class="row">
    <!-- Reporte de Donaciones por Período -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-donate"></i> Donaciones por Período</h3>
            </div>
            <div class="card-body">
                <p>Genera un reporte de todas las donaciones recibidas en un rango de fechas específico.</p>
                <form action="{{ route('inventario.reportes.donaciones.periodo') }}" method="GET">
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reporte de Inventario por Almacén -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-warehouse"></i> Inventario por Almacén</h3>
            </div>
            <div class="card-body">
                <p>Consulta el inventario actual de productos en almacenes.</p>
                <form action="{{ route('inventario.reportes.inventario.almacen') }}" method="GET">
                    <div class="form-group">
                        <label>Almacén (opcional)</label>
                        <select name="almacen_id" class="form-control">
                            <option value="">Todos los almacenes</option>
                            @foreach(\Modules\Inventario\Models\Almacene::all() as $almacen)
                                <option value="{{ $almacen->id_almacen }}">{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="btn-group btn-group-sm w-100 mt-4" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reporte de Solicitudes de Recolección -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck"></i> Solicitudes de Recolección</h3>
            </div>
            <div class="card-body">
                <p>Reporte de solicitudes de recolección filtradas por estado y fecha.</p>
                <form action="{{ route('inventario.reportes.solicitudes') }}" method="GET">
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En proceso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha Inicio (opcional)</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin (opcional)</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reporte de Salidas de Productos -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-arrow-right"></i> Salidas de Productos</h3>
            </div>
            <div class="card-body">
                <p>Reporte de productos que han salido del inventario.</p>
                <form action="{{ route('inventario.reportes.salidas') }}" method="GET">
                    <div class="form-group">
                        <label>Fecha Inicio (opcional)</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin (opcional)</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                    <div class="btn-group btn-group-sm w-100 mt-4" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reporte de Campañas -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn"></i> Campañas</h3>
            </div>
            <div class="card-body">
                <p>Reporte de campañas con sus respectivas donaciones.</p>
                <form action="{{ route('inventario.reportes.campanas') }}" method="GET">
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            <option value="">Todas</option>
                            <option value="activas">Activas</option>
                            <option value="finalizadas">Finalizadas</option>
                            <option value="proximas">Próximas</option>
                        </select>
                    </div>
                    <div class="btn-group btn-group-sm w-100 mt-5" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reporte de Distribución de Paquetes -->
    <div class="col-md-6 col-lg-4">
        <div class="card card-dark card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shipping-fast"></i> Distribución de Paquetes</h3>
            </div>
            <div class="card-body">
                <p>Dashboard completo de distribución mostrando destinos y estadísticas de envíos.</p>
                <form action="{{ route('inventario.reportes.distribucion') }}" method="GET">
                    <div class="form-group">
                        <label>Fecha Inicio (opcional)</label>
                        <input type="date" name="fecha_inicio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Fecha Fin (opcional)</label>
                        <input type="date" name="fecha_fin" class="form-control">
                    </div>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="submit" name="formato" value="ver" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver Dashboard
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card {
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        transition: all 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, .2);
    }
</style>
@stop




