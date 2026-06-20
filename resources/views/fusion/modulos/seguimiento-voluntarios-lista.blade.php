@extends('layouts.app')

@section('content_header_title', $tituloSeccion)
@section('content_header_subtitle', $seccion === 'voluntarios-inactivos' ? 'Brigadas sin actividad reciente' : 'Listado operativo de brigadistas')

@section('content')
<div class="{{ $seccion === 'voluntarios-inactivos' ? 'inactivos-theme' : '' }}">
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

@if($seccion === 'voluntarios-inactivos')
<div class="alert alert-light border mb-3 py-2">
    <strong class="text-danger">{{ $voluntarios->count() }}</strong> voluntarios inactivos registrados
</div>
@endif

<div class="card seg-list-card shadow-sm mb-3">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            @if($seccion === 'voluntarios-inactivos')
                <a href="{{ route('seguimiento.voluntarios') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Voluntarios activos
                </a>
            @else
                <span class="badge badge-light border mr-auto">{{ $voluntarios->count() }} registros</span>
                @if(\App\Support\FusionModuloAccess::canWriteSeguimientoSection($seccion))
                <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Agregar
                </a>
                @endif
            @endif
        </div>
    </div>
</div>

<div class="seg-filtros-panel">
    <form action="{{ route('seguimiento.' . $seccion) }}" method="GET" id="filtrosForm">
        <div class="form-group mb-3">
            <input type="search" name="q" class="form-control" placeholder="Buscar por nombre…" value="{{ request('q') }}">
        </div>
        <div class="seg-filtros-grid">
            <div>
                <label>CI</label>
                <input type="text" name="ci" class="form-control form-control-sm" placeholder="Buscar CI" value="{{ request('ci') }}">
            </div>
            <div>
                <label>Tipo de sangre</label>
                <select name="tipo_sangre" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $ts)
                        <option value="{{ $ts }}" {{ request('tipo_sangre') === $ts ? 'selected' : '' }}>{{ $ts }}</option>
                    @endforeach
                </select>
            </div>
            @if($seccion !== 'voluntarios-inactivos')
            <div>
                <label>Disponibilidad</label>
                <select name="estado" class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            @endif
            <div>
                <label>&nbsp;</label>
                <button type="button" class="btn btn-outline-secondary btn-sm btn-block" onclick="limpiarFiltros()">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </form>
</div>

<div class="seg-vol-grid">
    @forelse($voluntarios as $voluntario)
        @php
            $iniciales = mb_substr($voluntario->nombre ?? '', 0, 1, 'UTF-8') . mb_substr($voluntario->apellido ?? '', 0, 1, 'UTF-8');
            if (empty($iniciales)) { $iniciales = 'V'; }
            $activo = data_get($voluntario, 'activo');
        @endphp
        <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $voluntario->id_usuario]) }}" class="seg-vol-card">
            <div class="seg-vol-avatar"><span>{{ strtoupper($iniciales) }}</span></div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center flex-wrap" style="gap:0.35rem;">
                    <h4 class="mb-0">{{ $voluntario->nombre }} {{ $voluntario->apellido }}</h4>
                    <span class="seg-estado-badge {{ $activo ? 'activo' : 'inactivo' }}">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <small class="text-muted">CI: {{ data_get($voluntario, 'ci', 'N/D') }} · Sangre: {{ data_get($voluntario, 'tipo_sangre', 'N/D') }}</small>
            </div>
        </a>
    @empty
        <div class="col-12">
            <div class="alert alert-light border text-center py-4 mb-0">
                @if($seccion === 'voluntarios-inactivos')
                    <i class="far fa-smile fa-2x text-success mb-2 d-block"></i>
                    No hay voluntarios inactivos.
                @else
                    No se encontraron voluntarios.
                @endif
            </div>
        </div>
    @endforelse
</div>
</div>
@endsection

@push('js')
<script>
document.querySelectorAll('#filtrosForm select, #filtrosForm input[type="text"], #filtrosForm input[type="search"]').forEach(function (el) {
    el.addEventListener('change', function () { document.getElementById('filtrosForm').submit(); });
});
function limpiarFiltros() {
    document.querySelectorAll('#filtrosForm input, #filtrosForm select').forEach(function (el) { el.value = ''; });
    document.getElementById('filtrosForm').submit();
}
</script>
@endpush
