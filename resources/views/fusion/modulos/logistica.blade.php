@extends('layouts.app')

@section('content_header_title', 'Estadísticas')
@section('content_header_subtitle', 'Transporte y entrega de donaciones')

@section('content')
@php
    $kpiClasses = ['kpi-solicitudes', 'kpi-paquetes', 'kpi-seguimientos', 'kpi-vehiculos', 'kpi-conductores', 'kpi-reportes'];
@endphp

@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="row mb-3 logistica-kpi-row">
    @foreach($resumen as $index => $item)
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="small-box {{ $kpiClasses[$index] ?? 'kpi-solicitudes' }}">
            <div class="inner">
                <h3>{{ $item['total'] }}</h3>
                <p>{{ $item['label'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <a href="{{ route('logistica.crud.create', ['seccion' => 'solicitud']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Crear nueva solicitud
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive logistica-tabla-scroll">
            <table class="table table-sm table-hover logistica-tabla-operativa mb-0">
                <thead>
                    <tr>
                        <th class="col-ref">Nº</th>
                        <th class="col-estado">Estado</th>
                        <th class="col-caso">Solicitante / Destino</th>
                        <th class="col-emergencia">Emergencia</th>
                        <th class="col-fecha">Fecha</th>
                        @if($vistaIntegrada ?? false)
                        <th class="col-envio">Inventario</th>
                        @endif
                        <th class="col-acciones">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($solicitudesRecientes as $row)
                    <tr>
                        <td class="col-ref"><span class="text-muted font-weight-bold">{{ $row['ref'] }}</span></td>
                        <td class="col-estado"><span class="badge badge-{{ $row['estado_badge'] }}">{{ $row['estado_label'] }}</span></td>
                        <td class="col-caso">
                            <strong>{{ $row['solicitante_nombre'] }}</strong><br>
                            <small class="text-muted">{{ $row['destino_comunidad'] }}, {{ $row['destino_provincia'] }}</small>
                        </td>
                        <td class="col-emergencia">{{ $row['tipo_emergencia'] }}</td>
                        <td class="col-fecha">{{ $row['fecha_necesidad'] }}</td>
                        @if($vistaIntegrada ?? false)
                        <td class="col-envio">
                            @if($row['inventario_vinculado'] ?? false)
                                <span class="badge badge-secondary">{{ $row['inventario_paquete_estado'] ?? 'Vinculado' }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        @endif
                        <td class="col-acciones">
                            <span class="logistica-row-actions">
                                @if($row['paquete_logistica_id'] ?? false)
                                <a href="{{ route('logistica.seguimiento.tracking', ['id' => $row['paquete_logistica_id']]) }}" class="btn btn-outline-info btn-sm" title="Ver mapa">
                                    <i class="fas fa-map-marked-alt"></i>
                                </a>
                                @endif
                                <a href="{{ route('logistica.crud.edit', ['seccion' => 'solicitud', 'id' => $row['id_solicitud']]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ ($vistaIntegrada ?? false) ? 7 : 6 }}" class="text-muted text-center py-4">No hay solicitudes operativas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <a href="{{ route('logistica.solicitud') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-list"></i> Ver todas las solicitudes
        </a>
    </div>
</div>
@endsection
