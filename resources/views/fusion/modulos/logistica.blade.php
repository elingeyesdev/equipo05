@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .logistica-kpi .small-box {
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            margin-bottom: 0;
        }
        .logistica-kpi .small-box .inner {
            padding: 1rem 1rem 0.85rem;
        }
        .logistica-kpi .small-box h4 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            font-weight: 700;
        }
        .logistica-kpi .small-box p {
            margin-bottom: 0;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
        }
        .logistica-card {
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
            border: 0;
        }
        .logistica-card .card-header {
            border-bottom: 1px solid #e9ecef;
            background: rgba(13, 110, 253, 0.08);
            border-radius: 14px 14px 0 0;
        }
    </style>

    <div class="row mb-3">
        @foreach($resumen as $item)
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3 logistica-kpi">
            <div class="small-box bg-info">
                <div class="inner">
                    <h4>{{ $item['total'] }}</h4>
                    <p>{{ $item['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card logistica-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Logística — transporte de donaciones</h3>
                    <a href="{{ route('logistica.crud.create', ['seccion' => 'solicitud']) }}" class="btn btn-primary btn-sm ml-auto">
                        <i class="fa fa-plus"></i> Crear nueva solicitud
                    </a>
                </div>
                <div class="card-body">
                    <p class="mb-3">Panel operativo de transporte y entrega de donaciones.</p>
                    <div class="table-responsive logistica-tabla-scroll">
                        <table class="table table-sm table-striped logistica-tabla-operativa">
                            <thead class="thead-light">
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
                                        {{ $row['solicitante_nombre'] }}<br>
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
                                        <a href="{{ route('logistica.crud.edit', ['seccion' => 'solicitud', 'id' => $row['id_solicitud']]) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ ($vistaIntegrada ?? false) ? 7 : 6 }}" class="text-muted text-center">No hay solicitudes operativas registradas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('logistica.solicitud') }}" class="btn btn-outline-primary btn-sm">Ver todas las solicitudes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
