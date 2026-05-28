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
                <div class="card-header">
                    <h3 class="card-title mb-0">Logistica Transportacion Donaciones</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">Resumen operativo del modulo integrado desde <strong>@web</strong>.</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>ID Solicitud</th>
                                    <th>Estado</th>
                                    <th>Tipo Emergencia</th>
                                    <th>Fecha Necesidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitudesRecientes as $row)
                                <tr>
                                    <td>{{ $row->id_solicitud ?? '-' }}</td>
                                    <td>{{ $row->estado ?? '-' }}</td>
                                    <td>{{ $row->tipo_emergencia ?? '-' }}</td>
                                    <td>{{ $row->fecha_necesidad ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-muted">No hay solicitudes registradas todavia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
