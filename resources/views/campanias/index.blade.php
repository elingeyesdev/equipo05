@extends('layouts.app')

@section('title', 'Campañas')

@section('header')
    <h1>
        <i class="fas fa-bullhorn icon-title"></i>
        Campañas
    </h1>
    <p class="text-muted mb-0">
        Gestión de campañas, metas de recaudación y montos recaudados.
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
        $totalCampanias   = $campanias->count();
        $totalActivas     = $campanias->where('activa', 1)->count();
        $metaTotal        = $campanias->sum('metarecaudacion');
        $recaudadoTotal   = $campanias->sum('montorecaudado');
    @endphp

    {{-- RESUMEN --}}
    <div class="campanias-summary-grid">
        <div class="campanias-summary-card total-campanias">
            <h5>TOTAL CAMPAÑAS</h5>
            <div class="summary-value">
                {{ $totalCampanias }}
            </div>
            <small class="text-muted">
                Número total de campañas registradas.
            </small>
        </div>

        <div class="campanias-summary-card total-activas">
            <h5>CAMPAÑAS ACTIVAS</h5>
            <div class="summary-value">
                {{ $totalActivas }}
            </div>
            <small class="text-muted">
                Campañas marcadas como activas.
            </small>
        </div>

        <div class="campanias-summary-card total-meta">
            <h5>METAS Y RECAUDADO</h5>
            <div class="summary-value">
                Bs {{ number_format($recaudadoTotal, 2, ',', '.') }}
            </div>
            <small class="text-muted d-block">
                Meta total: Bs {{ number_format($metaTotal, 2, ',', '.') }}
            </small>
        </div>
    </div>

    {{-- TABLA PRINCIPAL --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de campañas
            </h3>
            <a href="{{ route('campanias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nueva campaña
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Fechas</th>
                        <th class="text-right">Meta</th>
                        <th class="text-right">Recaudado</th>
                        <th>Avance</th>
                        <th>Activa</th>
                        <th>Creador</th>
                        <th class="text-right" style="width: 150px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campanias as $campania)
                        @php
                            $meta   = $campania->metarecaudacion ?? 0;
                            $reca   = $campania->montorecaudado ?? 0;
                            $avance = $meta > 0 ? round(($reca / $meta) * 100) : 0;
                            $creador = optional($campania->creador);
                        @endphp
                        <tr>
                            <td>{{ $campania->campaniaid }}</td>
                            <td>
                                <strong>{{ $campania->titulo }}</strong><br>
                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit($campania->descripcion, 60) }}
                                </small>
                            </td>
                            <td>
                                <small>
                                    {{ $campania->fechainicio ?? '—' }}<br>
                                    <span class="text-muted">hasta</span><br>
                                    {{ $campania->fechafin ?? '—' }}
                                </small>
                            </td>
                            <td class="text-right">
                                Bs {{ number_format($meta, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                Bs {{ number_format($reca, 2, ',', '.') }}
                            </td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-success" style="width: {{ min($avance,100) }}%"></div>
                                </div>
                                <small>{{ $avance }}%</small>
                            </td>
                            <td>
                                @if($campania->activa)
                                    <span class="badge badge-success campania-badge-activa">Activa</span>
                                @else
                                    <span class="badge badge-secondary campania-badge-activa">Inactiva</span>
                                @endif
                            </td>
                            <td>
                                {{ $creador->nombre ?? '—' }} {{ $creador->apellido ?? '' }}<br>
                                <small class="text-muted">{{ $creador->email ?? '' }}</small>
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    {{-- <a href="{{ route('campanias.show', $campania->campaniaid) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a> --}}
                                    <a href="{{ route('campanias.edit', $campania->campaniaid) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('campanias.destroy', $campania->campaniaid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta campaña?');">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                No hay campañas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection