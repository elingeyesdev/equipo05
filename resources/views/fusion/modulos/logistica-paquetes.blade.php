@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="card-title mb-0"><i class="fas fa-boxes text-primary"></i> Paquetes logísticos</h3>
                <small class="text-muted">Vinculados a solicitudes y, cuando aplica, al inventario</small>
            </div>
            <div class="d-flex align-items-center mt-2 mt-md-0" style="gap: .5rem;">
                <span class="badge badge-primary">{{ $paquetes->count() }} registros</span>
                <a href="{{ route('logistica.crud.create', ['seccion' => 'paquete']) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nuevo paquete
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="btn-group btn-group-sm mb-3" role="group">
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter active" data-filter="todos">Todos</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="pendiente">Pendientes</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="armado">En almacén</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="camino">En tránsito</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="entregado">Entregados</button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Código paquete</th>
                            <th>Estado</th>
                            <th>Solicitud</th>
                            <th>Solicitante</th>
                            <th>Emergencia</th>
                            <th>Ubicación</th>
                            <th>Creación</th>
                            <th>Entrega</th>
                            <th>Inventario</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paquetes as $paquete)
                            <tr class="paquete-row" data-estado="{{ $paquete['estado_filtro'] }}">
                                <td><code>{{ $paquete['codigo'] }}</code></td>
                                <td><span class="badge badge-{{ $paquete['estado_badge'] }}">{{ $paquete['estado_nombre'] }}</span></td>
                                <td><code>{{ $paquete['codigo_seguimiento'] }}</code></td>
                                <td>{{ $paquete['solicitante_nombre'] }}<br><small class="text-muted">CI {{ $paquete['solicitante_ci'] }}</small></td>
                                <td>{{ $paquete['tipo_emergencia'] }}</td>
                                <td>{{ $paquete['ubicacion_actual'] }}</td>
                                <td>{{ $paquete['fecha_creacion'] }}</td>
                                <td>{{ $paquete['fecha_entrega'] }}</td>
                                <td>
                                    @if($paquete['inventario_paquete_codigo'])
                                        <code>{{ $paquete['inventario_paquete_codigo'] }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
                                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'paquete', 'id' => $paquete['id_paquete']]) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('logistica.crud.destroy', ['seccion' => 'paquete', 'id' => $paquete['id_paquete']]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">No hay paquetes operativos registrados.</td></tr>
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
