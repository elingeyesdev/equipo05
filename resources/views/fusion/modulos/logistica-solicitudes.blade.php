@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0"><i class="fas fa-clipboard-list text-primary"></i> Solicitudes de ayuda</h3>
                <small class="text-muted">
                    @if($vistaIntegrada ?? false)
                        Vista integrada logística e inventario (administrador)
                    @else
                        Flujo de transporte y entrega — el armado en almacén se gestiona en Inventario
                    @endif
                </small>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0 flex-wrap" style="gap: .5rem;">
                <span class="badge badge-primary">{{ $solicitudes->count() }} operativas</span>
                @if(($totalDemoOcultos ?? 0) > 0)
                    <span class="badge badge-secondary" title="Registros LOG-DEMO ocultos del listado operativo">{{ $totalDemoOcultos }} demo ocultas</span>
                @endif
                <a href="{{ route('logistica.solicitud.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva solicitud
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="logistica-filtros mb-3" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter active" data-filter="todos">Todas</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="pendiente">Pendientes</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="aprobada">Aprobadas</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="en_ruta">En ruta</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="entregada">Entregadas</button>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-solicitud-filter" data-filter="negada">Rechazadas</button>
            </div>

            <p class="logistica-scroll-hint mb-2"><i class="fas fa-arrows-alt-h mr-1"></i> Desliza horizontalmente para ver todas las columnas.</p>

            <div class="table-responsive logistica-tabla-scroll">
                <table class="table table-striped table-hover table-sm logistica-tabla-operativa" id="logisticaSolicitudesTable">
                    <thead class="thead-dark">
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
                                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'solicitud', 'id' => $solicitud['id_solicitud']]) }}" class="btn btn-warning btn-xs" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('logistica.crud.destroy', ['seccion' => 'solicitud', 'id' => $solicitud['id_solicitud']]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta solicitud?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
