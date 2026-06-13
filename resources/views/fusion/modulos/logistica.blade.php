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
                    <p class="mb-3">Panel operativo de transporte y entrega de donaciones. Las solicitudes mostradas excluyen registros de demostración (<code>LOG-DEMO-*</code>).</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Estado</th>
                                    <th>Solicitante</th>
                                    <th>Emergencia</th>
                                    <th>Destino</th>
                                    <th>Fecha necesidad</th>
                                    <th>Inventario</th>
                                    <th style="width: 90px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudesRecientes as $row)
                                <tr>
                                    <td><code>{{ $row['codigo_seguimiento'] }}</code></td>
                                    <td><span class="badge badge-{{ $row['estado_badge'] }}">{{ $row['estado_label'] }}</span></td>
                                    <td>{{ $row['solicitante_nombre'] }}</td>
                                    <td>{{ $row['tipo_emergencia'] }}</td>
                                    <td>{{ $row['destino_comunidad'] }}, {{ $row['destino_provincia'] }}</td>
                                    <td>{{ $row['fecha_necesidad'] }}</td>
                                    <td>
                                        @if($row['inventario_paquete_codigo'])
                                            <code>{{ $row['inventario_paquete_codigo'] }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('logistica.crud.edit', ['seccion' => 'solicitud', 'id' => $row['id_solicitud']]) }}" class="btn btn-warning btn-xs"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-muted text-center">No hay solicitudes operativas registradas.</td>
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
