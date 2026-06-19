@extends('layouts.app')

@section('content_header_title', 'Paquetes logísticos')
@section('content_header_subtitle', 'Vinculados a solicitudes y, cuando aplica, al inventario')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">{{ $paquetes->count() }} registros</span>
            <a href="{{ route('logistica.crud.create', ['seccion' => 'paquete']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo paquete
            </a>
        </div>
    </div>
    <div class="card-body pb-0">
        <div class="logistica-filtros" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm btn-paquete-filter active" data-filter="todos">Todos</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-paquete-filter" data-filter="pendiente">Pendientes</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-paquete-filter" data-filter="armado">En almacén</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-paquete-filter" data-filter="camino">En tránsito</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-paquete-filter" data-filter="entregado">Entregados</button>
        </div>
        <p class="logistica-scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i> Desliza horizontalmente para ver todas las columnas.</p>
    </div>
    <div class="card-body p-0 pt-0">
        <div class="table-responsive logistica-tabla-scroll">
            <table class="table table-hover table-sm logistica-tabla-operativa mb-0">
                <thead>
                    <tr>
                        <th class="col-ref">Nº</th>
                        <th class="col-estado">Estado</th>
                        <th class="col-caso">Solicitud / Solicitante</th>
                        <th class="col-emergencia">Emergencia</th>
                        <th class="col-caso">Ubicación</th>
                        <th class="col-fecha">Creación</th>
                        <th class="col-fecha">Entrega</th>
                        @if($vistaIntegrada ?? false)
                        <th class="col-envio">Inventario</th>
                        @endif
                        <th class="col-acciones text-right">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paquetes as $paquete)
                        <tr class="paquete-row" data-estado="{{ $paquete['estado_filtro'] }}">
                            <td class="col-ref"><span class="text-muted font-weight-bold">{{ $paquete['ref'] }}</span></td>
                            <td class="col-estado"><span class="badge badge-{{ $paquete['estado_badge'] }}">{{ $paquete['estado_nombre'] }}</span></td>
                            <td class="col-caso">
                                {{ $paquete['solicitud_ref'] ?? '—' }}<br>
                                <strong>{{ $paquete['solicitante_nombre'] }}</strong>
                            </td>
                            <td class="col-emergencia">{{ $paquete['tipo_emergencia'] }}</td>
                            <td class="col-caso">{{ $paquete['ubicacion_actual'] }}</td>
                            <td class="col-fecha">{{ $paquete['fecha_creacion'] }}</td>
                            <td class="col-fecha">{{ $paquete['fecha_entrega'] }}</td>
                            @if($vistaIntegrada ?? false)
                            <td class="col-envio">
                                @if($paquete['inventario_vinculado'] ?? false)
                                    <span class="badge badge-secondary">{{ $paquete['inventario_paquete_estado'] ?? 'Vinculado' }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endif
                            <td class="col-acciones text-right text-nowrap">
                                <span class="logistica-row-actions">
                                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'paquete', 'id' => $paquete['id_paquete']]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('logistica.crud.destroy', ['seccion' => 'paquete', 'id' => $paquete['id_paquete']]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ ($vistaIntegrada ?? false) ? 9 : 8 }}" class="text-center text-muted py-4">No hay paquetes operativos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-paquete-filter');
    const rows = document.querySelectorAll('.paquete-row');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            const value = btn.dataset.filter;
            rows.forEach((row) => {
                row.style.display = (value === 'todos' || row.dataset.estado === value) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
@endsection
