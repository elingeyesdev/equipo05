@extends('layouts.app')

@section('title', 'Resumen de campaña')

@section('content')
<div class="container mt-4">
    <h2>Resumen de campaña</h2>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- Datos de la campaña --}}
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="card-title">{{ $campania->titulo }}</h4>
            <p class="mb-1"><strong>Descripción:</strong> {{ $campania->descripcion }}</p>
            <p class="mb-1">
                <strong>Fechas:</strong>
                {{ $campania->fechainicio ?? '—' }}
                -
                {{ $campania->fechafin ?? 'Sin fecha de cierre' }}
            </p>
            <p class="mb-1">
                <strong>Meta:</strong> {{ number_format($campania->metarecaudacion, 2) }} Bs
            </p>
            <p class="mb-1">
                <strong>Estado:</strong>
                @if ($campania->activa)
                    <span class="badge bg-success">ACTIVA</span>
                @else
                    <span class="badge bg-secondary">CERRADA</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Resumen financiero --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($totales['total_donado'], 2) }} Bs</h3>
                    <p>Total donado</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totales['total_asignado'], 2) }} Bs</h3>
                    <p>Total asignado a gastos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($totales['total_saldo'], 2) }} Bs</h3>
                    <p>Saldo disponible en donaciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de donaciones de la campaña --}}
    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Detalle de donaciones</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donante</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Asignado</th>
                        <th>Saldo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donaciones as $donacion)
                        @php
                            $asignado = $donacion->asignacionesPivot
                                ? $donacion->asignacionesPivot->sum('montoasignado')
                                : 0;
                            $saldo = optional($donacion->saldo)->saldodisponible ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $donacion->donacionid }}</td>
                            <td>
                                @if ($donacion->esanonima)
                                    Anónimo
                                @else
                                    {{ optional($donacion->usuario)->nombre }} {{ optional($donacion->usuario)->apellido }}
                                @endif
                            </td>
                            <td>{{ $donacion->fechadonacion ?? '—' }}</td>
                            <td>{{ number_format($donacion->monto, 2) }} Bs</td>
                            <td>{{ number_format($asignado, 2) }} Bs</td>
                            <td>{{ number_format($saldo, 2) }} Bs</td>
                            <td>{{ optional($donacion->estado)->nombre ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay donaciones registradas para esta campaña.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Botones --}}
    <div class="d-flex justify-content-between">
        <a href="{{ route('campanias.cierreSeleccion') }}" class="btn btn-secondary">
            ⬅ Volver a selección de campaña
        </a>

        @if ($campania->activa)
            <form action="{{ route('campanias.cerrar', $campania->campaniaid) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('¿Seguro que deseas cerrar esta campaña?')">
                    Cerrar campaña
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
