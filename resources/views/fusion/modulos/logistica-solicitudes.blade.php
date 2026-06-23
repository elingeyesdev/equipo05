@extends('layouts.app')

@section('content_header_title', 'Solicitudes de ayuda')
@section('content_header_subtitle', ($vistaIntegrada ?? false)
    ? 'Vista integrada logística e inventario'
    : 'Flujo de transporte y entrega de donaciones')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            @if(($totalDemoOcultos ?? 0) > 0)
                <span class="badge badge-secondary mr-auto" title="Registros LOG-DEMO ocultos del listado operativo">{{ $totalDemoOcultos }} demo ocultas</span>
            @else
                <span class="badge badge-light border mr-auto">{{ $solicitudes->count() }} operativas</span>
            @endif
            <a href="{{ route('logistica.solicitud.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva solicitud
            </a>
        </div>
    </div>
    <div class="card-body pb-0">
        <div class="logistica-filtros" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter active" data-filter="todos">Todas</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="pendiente">Pendientes</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="aprobada">Aprobadas</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="en_ruta">En ruta</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="entregada">Entregadas</button>
            <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="negada">Rechazadas</button>
        </div>
        <p class="logistica-scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i> Desliza horizontalmente para ver todas las columnas.</p>
    </div>
    <div class="card-body p-0 pt-0">
        <div class="table-responsive logistica-tabla-scroll">
            <table class="table table-hover table-sm logistica-tabla-operativa mb-0" id="logisticaSolicitudesTable">
                <thead>
                    <tr>
                        <th class="col-ref">Nº</th>
                        <th class="col-estado">Estado</th>
                        <th class="col-caso">Solicitante / Destino</th>
                        <th class="col-emergencia">Emergencia</th>
                        <th class="col-num">Afect.</th>
                        <th class="col-fecha">Fecha</th>
                        <th class="col-envio">{{ ($vistaIntegrada ?? false) ? 'Envío (log. + inv.)' : 'Transporte' }}</th>
                        <th class="col-acciones text-right">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudes as $solicitud)
                        <tr class="solicitud-row" data-estado="{{ $solicitud['estado_filtro'] }}">
                            <td class="col-ref"><span class="text-muted font-weight-bold">{{ $solicitud['ref'] }}</span></td>
                            <td class="col-estado">
                                <span class="badge badge-{{ $solicitud['estado_badge'] }}">{{ $solicitud['estado_label'] }}</span>
                            </td>
                            <td class="col-caso">
                                <strong>{{ $solicitud['solicitante_nombre'] }}</strong><br>
                                <small class="text-muted">{{ $solicitud['destino_comunidad'] }}, {{ $solicitud['destino_provincia'] }}</small><br>
                                <small class="text-muted">CI {{ $solicitud['solicitante_ci'] }}</small>
                            </td>
                            <td class="col-emergencia">{{ $solicitud['tipo_emergencia'] }}</td>
                            <td class="col-num">{{ $solicitud['cantidad_personas'] }}</td>
                            <td class="col-fecha">{{ $solicitud['fecha_necesidad'] }}</td>
                            <td class="col-envio">
                                <span class="badge badge-{{ $solicitud['envio_badge'] ?? 'secondary' }}">{{ $solicitud['envio_label'] ?? '—' }}</span>
                                @if(($vistaIntegrada ?? false) && !empty($solicitud['envio_detalle']))
                                    <br><small class="text-muted">{{ $solicitud['envio_detalle'] }}</small>
                                @endif
                            </td>
                            <td class="col-acciones text-right text-nowrap">
                                <span class="logistica-row-actions">
                                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'solicitud', 'id' => $solicitud['id_solicitud']]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('logistica.crud.destroy', ['seccion' => 'solicitud', 'id' => $solicitud['id_solicitud']]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta solicitud?');">
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
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay solicitudes operativas. Cree una desde el formulario o el acceso público de ayuda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-solicitud-filter');
    const rows = document.querySelectorAll('.solicitud-row');
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
