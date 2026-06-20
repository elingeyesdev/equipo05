@extends('layouts.app')

@section('content_header_title', 'Administradores')
@section('content_header_subtitle', 'Gestión de cuentas con permisos del módulo')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card shadow-sm mb-3">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">{{ $administradores->count() }} registros</span>
            @if(\App\Support\FusionModuloAccess::canWriteSeguimientoSection($seccion))
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Agregar administrador
            </a>
            @endif
        </div>
    </div>
</div>

<div class="seg-filtros-panel">
    <form action="{{ route('seguimiento.administradores') }}" method="GET" id="filterForm">
        <div class="seg-filtros-grid">
            <div>
                <label>Nombre</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Buscar por nombre">
            </div>
            <div>
                <label>CI</label>
                <input type="text" name="ci" value="{{ request('ci') }}" class="form-control form-control-sm" placeholder="Buscar CI">
            </div>
            <div>
                <label>Estado</label>
                <select name="estado" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-block" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="seg-admin-grid">
    @forelse($administradores as $admin)
        @php
            $iniciales = mb_substr($admin->nombre ?? '', 0, 1, 'UTF-8') . mb_substr($admin->apellido ?? '', 0, 1, 'UTF-8');
            if (empty($iniciales)) { $iniciales = 'AD'; }
        @endphp
        <div class="seg-admin-card shadow-sm">
            <div class="d-flex align-items-start mb-2">
                <div class="seg-vol-avatar mr-3" style="background:#4f46e5;">{{ strtoupper($iniciales) }}</div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center flex-wrap" style="gap:0.35rem;">
                        <strong>{{ $admin->nombre }} {{ $admin->apellido }}</strong>
                        <span class="badge badge-info">Admin</span>
                        <span class="seg-estado-badge {{ $admin->activo ? 'activo' : 'inactivo' }}">{{ $admin->activo ? 'Activo' : 'Inactivo' }}</span>
                    </div>
                    <small class="text-muted d-block mt-1"><i class="fas fa-envelope mr-1"></i>{{ $admin->email ?: 'Sin correo' }}</small>
                    <small class="text-muted d-block"><i class="fas fa-id-card mr-1"></i>CI: {{ data_get($admin, 'ci') ?: 'N/D' }}</small>
                    @if(!empty(data_get($admin, 'telefono')))
                        <small class="text-muted d-block"><i class="fas fa-phone mr-1"></i>{{ data_get($admin, 'telefono') }}</small>
                    @endif
                </div>
            </div>
            <div class="seg-btn-toolbar justify-content-end mt-2 pt-2 border-top">
                <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $admin->id_usuario]) }}" class="btn btn-outline-secondary btn-sm" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $admin->id_usuario]) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="toggle_active" value="1">
                    @if($admin->activo)
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Desactivar administrador?');">
                            <i class="fas fa-ban"></i> Desactivar
                        </button>
                    @else
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-check"></i> Activar
                        </button>
                    @endif
                </form>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-light border text-center py-4 mb-0">
                <i class="fas fa-user-shield fa-2x text-muted mb-2 d-block"></i>
                No se encontraron administradores con los filtros aplicados.
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('js')
<script>
document.querySelectorAll('#filterForm select, #filterForm input').forEach(function (el) {
    el.addEventListener('change', function () { document.getElementById('filterForm').submit(); });
});
function limpiarFiltros() {
    document.querySelectorAll('#filterForm input, #filterForm select').forEach(function (el) { el.value = ''; });
    document.getElementById('filterForm').submit();
}
</script>
@endpush
