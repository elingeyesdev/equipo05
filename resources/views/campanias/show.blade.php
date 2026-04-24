@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Detalle de Campaña</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h4>{{ $campania->titulo }}</h4>
            <p>{{ $campania->descripcion }}</p>

            <p><strong>Meta:</strong> {{ number_format($campania->metarecaudacion,2) }} Bs</p>

            @php
                $totalDonado = $campania->donaciones->sum('monto');
            @endphp

            <p><strong>Total recaudado:</strong> {{ number_format($totalDonado,2) }} Bs</p>
            <p><strong>Activa:</strong> {{ $campania->activa ? 'Sí' : 'No' }}</p>
            <p><strong>Fecha inicio:</strong> {{ $campania->fechainicio }}</p>
            <p><strong>Fecha fin:</strong> {{ $campania->fechafin }}</p>
        </div>
    </div>

    <a href="{{ route('campanias.reporteGeneral') }}" class="btn btn-info">Volver al reporte general</a>
    <a href="{{ route('campanias.index') }}" class="btn btn-secondary">Volver al listado</a>
</div>
@endsection
