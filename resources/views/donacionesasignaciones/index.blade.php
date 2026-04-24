@extends('layouts.app')

@section('title', 'Donaciones asignadas')

@section('header')
    <h1>
        <i class="fas fa-link icon-title"></i>
        Donaciones asignadas
    </h1>
    <p class="text-muted mb-0">
        Relación entre las donaciones y las asignaciones donde fueron utilizadas.
    </p>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $totalAsignado  = $donasig->sum('montoasignado');
        $totalRegistros = $donasig->count();
    @endphp

    {{-- RESUMEN --}}
    <div class="donasig-summary-grid">
        <div class="donasig-summary-card total-asignado">
            <h5>TOTAL ASIGNADO</h5>
            <div class="summary-value">
                Bs {{ number_format($totalAsignado, 2, ',', '.') }}
            </div>
            <small class="text-muted">
                Suma de todos los montos asignados desde las donaciones.
            </small>
        </div>

        <div class="donasig-summary-card total-registros">
            <h5>TOTAL REGISTROS</h5>
            <div class="summary-value">{{ $totalRegistros }}</div>
            <small class="text-muted">
                Número total de asignaciones hechas desde donaciones.
            </small>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Lista de asignaciones por donación
            </h3>
            <a href="{{ route('donacionesasignaciones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo registro
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donación</th>
                        <th>Asignación</th>
                        <th class="text-right">Monto asignado</th>
                        <th>Fecha</th>
                        <th class="text-right" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donasig as $rel)
                        @php
                            $don = optional($rel->donacion);
                            $asig = optional($rel->asignacion);
                        @endphp
                        <tr>
                            <td>{{ $rel->donacionasignacionid }}</td>

                            <td>
                                <strong>#{{ $don->donacionid }}</strong><br>
                                <small class="text-muted">
                                    {{ $don->tipodonacion }} — Bs {{ number_format($don->monto,2,',','.') }}
                                </small><br>
                                @if($don->usuario)
                                    <small>{{ $don->usuario->nombre }} {{ $don->usuario->apellido }}</small>
                                @else
                                    <small class="text-muted">Anónima</small>
                                @endif
                            </td>

                            <td>
                                <strong>#{{ $asig->asignacionid }}</strong><br>
                                <small class="text-muted">{{ $asig->descripcion }}</small>
                            </td>

                            <td class="text-right">
                                <span class="badge badge-success">
                                    Bs {{ number_format($rel->montoasignado, 2, ',', '.') }}
                                </span>
                            </td>

                            <td>{{ $rel->fechaasignacion }}</td>

                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('donacionesasignaciones.edit', $rel->donacionasignacionid) }}"
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('donacionesasignaciones.destroy', $rel->donacionasignacionid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este registro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay registros aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
