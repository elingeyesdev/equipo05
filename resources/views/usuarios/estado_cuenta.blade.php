@extends('layouts.app')

@section('title', 'Estado de cuenta del donante')

@section('header')
    <h1>
        <i class="fas fa-file-invoice-dollar icon-title"></i>
        Estado de cuenta del donante
    </h1>
    <p class="text-muted mb-0">
        Resumen de donaciones, montos asignados y saldos disponibles.
    </p>
@endsection

@section('content')

    {{-- DATOS DEL USUARIO --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between flex-wrap">
            <div class="mb-2">
                <h5 class="mb-1">
                    <i class="fas fa-user-circle mr-1 text-primary"></i>
                    {{ $usuario->nombre }} {{ $usuario->apellido }}
                </h5>
                <p class="mb-1"><strong>Email:</strong> {{ $usuario->email }}</p>
                <p class="mb-1"><strong>Teléfono:</strong> {{ $usuario->telefono ?? '—' }}</p>
                <p class="mb-0"><strong>Activo:</strong> {{ $usuario->activo ? 'Sí' : 'No' }}</p>
            </div>
            <div class="mb-2 text-right">
                <a href="{{ route('usuarios.estadoCuentaSeleccion') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Cambiar usuario
                </a>
            </div>
        </div>
    </div>

    {{-- RESUMEN GENERAL (usa los mismos totales que ya tenías) --}}
    <div class="saldo-summary-grid">
        <div class="saldo-summary-card total-original">
            <h5>TOTAL DONADO</h5>
            <div class="saldo-value">
                Bs {{ number_format($totales['total_donado'] ?? 0, 2, ',', '.') }}
            </div>
            <small class="text-muted">Suma de todas las donaciones del usuario.</small>
        </div>

        <div class="saldo-summary-card total-utilizado">
            <h5>TOTAL ASIGNADO</h5>
            <div class="saldo-value">
                Bs {{ number_format($totales['total_asignado'] ?? 0, 2, ',', '.') }}
            </div>
            <small class="text-muted">Monto utilizado en asignaciones / gastos.</small>
        </div>

        <div class="saldo-summary-card total-disponible">
            <h5>SALDO DISPONIBLE</h5>
            <div class="saldo-value">
                Bs {{ number_format($totales['total_saldo'] ?? 0, 2, ',', '.') }}
            </div>
            <small class="text-muted">Fondos que aún quedan disponibles.</small>
        </div>
    </div>

    {{-- TABLA DE DONACIONES --}}
    <div class="card">
        <div class="card-header bg-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-hand-holding-heart mr-1"></i>
                Detalle por donación
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Campaña</th>
                        <th>Fecha</th>
                        <th class="text-right">Monto donado</th>
                        <th class="text-right">Asignado</th>
                        <th class="text-right">Saldo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donaciones as $donacion)
                        @php
                            $saldoDisponible = optional($donacion->saldo)->saldodisponible ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $donacion->donacionid }}</td>
                            <td>{{ optional($donacion->campania)->titulo ?? '—' }}</td>
                            <td>{{ $donacion->fechadonacion ?? '—' }}</td>
                            <td class="text-right">
                                Bs {{ number_format($donacion->monto, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                <span class="badge badge-warning">
                                    Bs {{ number_format($donacion->total_asignado ?? 0, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <span class="badge badge-success">
                                    Bs {{ number_format($saldoDisponible, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ optional($donacion->estado)->nombre ?? '—' }}
                                </span>
                            </td>
                        </tr>

                        {{-- Asignaciones y detalles debajo de cada donación --}}
                        @if ($donacion->asignacionesPivot && $donacion->asignacionesPivot->count())
                            <tr>
                                <td colspan="7">
                                    <strong>Asignaciones de esta donación:</strong>
                                    <ul class="mb-0">
                                        @foreach ($donacion->asignacionesPivot as $pivot)
                                            @php
                                                $asignacion = $pivot->asignacion;
                                            @endphp
                                            <li class="mt-2">
                                                <strong>Asignación #{{ $asignacion->asignacionid ?? $pivot->asignacionid }}</strong>
                                                – {{ $asignacion->descripcion ?? 'Sin descripción' }}
                                                – Monto asignado desde esta donación:
                                                <strong>
                                                    Bs {{ number_format($pivot->montoasignado, 2, ',', '.') }}
                                                </strong>

                                                @if ($asignacion && $asignacion->detalles && $asignacion->detalles->count())
                                                    <br>
                                                    <span>Detalles de gasto:</span>
                                                    <ul class="mb-1">
                                                        @foreach ($asignacion->detalles as $det)
                                                            <li>
                                                                {{ $det->concepto }}
                                                                ({{ $det->cantidad }} x
                                                                 Bs {{ number_format($det->preciounitario, 2, ',', '.') }})
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Este usuario no tiene donaciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer text-right">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver a usuarios
            </a>
        </div>
    </div>
@endsection
