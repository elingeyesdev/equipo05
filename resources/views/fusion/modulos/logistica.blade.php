@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        @foreach($resumen as $item)
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
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
            <div class="card">
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
