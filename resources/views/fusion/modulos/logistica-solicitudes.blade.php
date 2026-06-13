@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0"><i class="fas fa-clipboard-list text-primary"></i> Solicitudes de ayuda</h3>
                <small class="text-muted">Solicitudes reales del flujo logístico e inventario</small>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0" style="gap: .5rem;">
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

            <div class="btn-group btn-group-sm mb-3" role="group">
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter active" data-filter="todos">Todas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="pendiente">Pendientes</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="aprobada">Aprobadas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="en_ruta">En ruta</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="entregada">Entregadas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="negada">Rechazadas</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm" id="logisticaSolicitudesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código</th>
                            <th>Estado</th>
                            <th>Solicitante</th>
                            <th>Destino</th>
                            <th>Emergencia</th>
                            <th>Afectados</th>
                            <th>Fecha necesidad</th>
                            <th>Paquete logística</th>
                            <th>Paquete inventario</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr class="solicitud-row" data-estado="{{ $solicitud['estado_filtro'] }}">
                                <td><code>{{ $solicitud['codigo_seguimiento'] }}</code></td>
                                <td>
                                    <span class="badge badge-{{ $solicitud['estado_badge'] }}">{{ $solicitud['estado_label'] }}</span>
                                </td>
                                <td>
                                    <strong>{{ $solicitud['solicitante_nombre'] }}</strong><br>
                                    <small class="text-muted">CI: {{ $solicitud['solicitante_ci'] }} · {{ $solicitud['solicitante_telefono'] }}</small>
                                </td>
                                <td>
                                    {{ $solicitud['destino_comunidad'] }}<br>
                                    <small class="text-muted">{{ $solicitud['destino_provincia'] }}</small>
                                </td>
                                <td>{{ $solicitud['tipo_emergencia'] }}</td>
                                <td class="text-center">{{ $solicitud['cantidad_personas'] }}</td>
                                <td>{{ $solicitud['fecha_necesidad'] }}</td>
                                <td>
                                    @if($solicitud['paquete_logistica_codigo'])
                                        <code>{{ $solicitud['paquete_logistica_codigo'] }}</code>
                                        @if($solicitud['paquete_estado'])
                                            <br><small class="text-muted">{{ $solicitud['paquete_estado'] }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sin paquete</span>
                                    @endif
                                </td>
                                <td>
                                    @if($solicitud['inventario_paquete_codigo'])
                                        <code>{{ $solicitud['inventario_paquete_codigo'] }}</code>
                                        @if($solicitud['inventario_paquete_estado'])
                                            <br><small class="text-muted">{{ ucfirst($solicitud['inventario_paquete_estado']) }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
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
                                <td colspan="10" class="text-center text-muted py-4">
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
