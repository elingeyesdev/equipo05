@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Reporte General de Campañas</h2>

    {{-- TARJETAS RESUMEN GENERAL --}}
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Meta total</h6>
                    <h3>{{ number_format($metaTotal, 2) }} Bs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Recaudado total</h6>
                    <h3>{{ number_format($recaudadoTotal, 2) }} Bs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6>Faltante total</h6>
                    <h3>{{ number_format($faltanteTotal, 2) }} Bs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6>Saldo disponible total</h6>
                    <h3>{{ number_format($saldoTotal, 2) }} Bs</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA DE CAMPAÑAS --}}
    <div class="card mt-4">
        <div class="card-header">
            <h5>Detalle por campaña</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Campaña</th>
                        <th>Meta</th>
                        <th>Recaudado</th>
                        <th>Faltante</th>
                        <th>Asignado</th>
                        <th>Saldo disponible</th>
                        <th>Donaciones</th>
                        <th>% Avance</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campanias as $c)
                        @php
                            $recaudado = $c->total_donado ?? 0;
                            $asignado  = $c->total_asignado ?? 0;
                            $faltante  = max(0, $c->metarecaudacion - $recaudado);
                            $saldo     = $recaudado - $asignado;
                            $porc      = $c->metarecaudacion > 0 
                                            ? round(($recaudado / $c->metarecaudacion) * 100, 2)
                                            : 0;
                        @endphp
                        <tr>
                            <td>{{ $c->titulo }}</td>
                            <td>{{ number_format($c->metarecaudacion, 2) }} Bs</td>
                            <td>{{ number_format($recaudado, 2) }} Bs</td>
                            <td>{{ number_format($faltante, 2) }} Bs</td>
                            <td>{{ number_format($asignado, 2) }} Bs</td>
                            <td>{{ number_format($saldo, 2) }} Bs</td>
                            <td>{{ $c->cantidad_donaciones }}</td>
                            <td>
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar 
                                        @if($porc < 50) bg-danger 
                                        @elseif($porc < 80) bg-warning 
                                        @else bg-success @endif" 
                                        role="progressbar"
                                        style="width: {{ $porc }}%;">
                                        {{ $porc }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($c->activa)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Cerrada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No hay campañas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
