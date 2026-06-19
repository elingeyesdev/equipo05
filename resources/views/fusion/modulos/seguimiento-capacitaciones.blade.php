@extends('layouts.app')

@section('content_header_title', 'Capacitaciones')
@section('content_header_subtitle', 'Programas de entrenamiento y cursos de brigada')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card shadow-sm">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">{{ $capacitaciones->count() }} registros</span>
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Crear capacitación
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover seg-data-table mb-0">
                <thead>
                    <tr>
                        <th style="width:4rem;">#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th style="width:7rem;">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($capacitaciones as $idx => $cap)
                        <tr>
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td class="font-weight-bold">{{ $cap->nombre }}</td>
                            <td class="text-muted">{{ $cap->descripcion ?: '—' }}</td>
                            <td>
                                <span class="seg-row-actions">
                                    <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $cap->id_capacitacion]) }}" class="btn btn-outline-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('seguimiento.crud.destroy', ['seccion' => $seccion, 'id' => $cap->id_capacitacion]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar capacitación?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>
                                No hay capacitaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
