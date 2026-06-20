@extends('layouts.app')

@section('content_header_title', 'Necesidades')
@section('content_header_subtitle', 'Insumos y apoyo requerido por las brigadas')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card seg-accent-warning shadow-sm">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">{{ $necesidades->count() }} registros</span>
            @if(\App\Support\FusionModuloAccess::canWriteSeguimientoSection($seccion))
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Crear necesidad
            </a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover seg-data-table mb-0">
                <thead>
                    <tr>
                        <th style="width:4rem;">#</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                        <th style="width:7rem;">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($necesidades as $idx => $nec)
                        <tr>
                            <td class="text-muted">{{ $idx + 1 }}</td>
                            <td>{{ $nec->descripcion ?: '—' }}</td>
                            <td><span class="badge badge-light border">{{ $nec->tipo ?: 'Otro' }}</span></td>
                            <td>
                                <span class="seg-row-actions">
                                    <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $nec->id_necesidad]) }}" class="btn btn-outline-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('seguimiento.crud.destroy', ['seccion' => $seccion, 'id' => $nec->id_necesidad]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar necesidad?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                                No hay necesidades registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
