@extends('layouts.app')

@section('content_header_title', 'Centro de soporte')
@section('content_header_subtitle', 'Consultas y tickets de voluntarios')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="row seg-kpi-row mb-3">
    @foreach(['abierta' => 'kpi-alertas', 'en_proceso' => 'kpi-consultas', 'resuelta' => 'kpi-evaluaciones', 'cerrada' => 'kpi-vol-inactivos'] as $est => $kpiClass)
        <div class="col-6 col-md-3 mb-2">
            <div class="small-box {{ $kpiClass }} mb-0">
                <div class="inner">
                    <h3>{{ $conteoEstados[$est] ?? 0 }}</h3>
                    <p class="text-capitalize">{{ str_replace('_', ' ', $est) }}</p>
                </div>
                <div class="icon"><i class="fas fa-ticket-alt"></i></div>
            </div>
        </div>
    @endforeach
</div>

<div class="card seg-list-card shadow-sm">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            <h3 class="card-title mb-0 mr-auto"><i class="fas fa-life-ring mr-1 text-warning"></i> Consultas</h3>
            @if(\App\Support\FusionModuloAccess::canWriteSeguimientoSection($seccion))
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-plus"></i> Nueva consulta
            </a>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover seg-data-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asunto</th>
                        <th>Voluntario</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultas as $c)
                        @php
                            $est = strtolower($c->estado ?? 'abierta');
                            $pri = strtolower($c->prioridad ?? 'media');
                        @endphp
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>
                                <strong>{{ $c->asunto }}</strong>
                                @if(!empty($c->descripcion))
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($c->descripcion, 80) }}</div>
                                @endif
                            </td>
                            <td>{{ trim(($c->vol_nombre ?? '').' '.($c->vol_apellido ?? '')) ?: '—' }}</td>
                            <td><span class="badge badge-pill badge-prioridad-{{ $pri }}">{{ $pri }}</span></td>
                            <td><span class="badge badge-pill badge-estado-{{ $est }}">{{ str_replace('_', ' ', $est) }}</span></td>
                            <td>{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $c->id]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay consultas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
