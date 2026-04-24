@extends('layouts.app')

@section('title', 'Saldos de donaciones')

@section('header')
    <h1>
        <i class="fas fa-wallet icon-title"></i>
        Saldos de donaciones
    </h1>
    <p class="text-muted mb-0">
        Control de montos originales, utilizados y saldos disponibles de cada donación.
    </p>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $totalOriginal   = $saldos->sum('montooriginal');
        $totalUtilizado  = $saldos->sum('montoutilizado');
        $totalDisponible = $saldos->sum('saldodisponible');
    @endphp

    {{-- RESUMEN GENERAL --}}
    <div class="saldo-summary-grid">
        <div class="saldo-summary-card total-original">
            <h5>MONTO ORIGINAL</h5>
            <div class="saldo-value">
                Bs {{ number_format($totalOriginal, 2, ',', '.') }}
            </div>
            <small class="text-muted">Suma de todos los montos donados</small>
        </div>

        <div class="saldo-summary-card total-utilizado">
            <h5>MONTO UTILIZADO</h5>
            <div class="saldo-value">
                Bs {{ number_format($totalUtilizado, 2, ',', '.') }}
            </div>
            <small class="text-muted">Total ya asignado a campañas / gastos</small>
        </div>

        <div class="saldo-summary-card total-disponible">
            <h5>SALDO DISPONIBLE</h5>
            <div class="saldo-value">
                Bs {{ number_format($totalDisponible, 2, ',', '.') }}
            </div>
            <small class="text-muted">Fondos aún disponibles para asignar</small>
        </div>
    </div>

    {{-- TABLA DETALLADA --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Detalle por donación
            </h3>
            <a href="{{ route('saldosdonaciones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo saldo
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donación</th>
                        <th>Donante</th>
                        <th>Campaña</th>
                        <th class="text-right">Monto original</th>
                        <th class="text-right">Utilizado</th>
                        <th class="text-right">Disponible</th>
                        <th>Última actualización</th>
                        <th class="text-right" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saldos as $saldo)
                        @php
                            $don = $saldo->donacion;
                            $usuario  = optional($don)->usuario;
                            $campania = optional($don)->campania;
                        @endphp
                        <tr>
                            <td>{{ $saldo->saldoid }}</td>
                            <td>
                                #{{ optional($don)->donacionid ?? '—' }}<br>
                                <small class="text-muted">
                                    {{ optional($don)->tipodonacion }} —
                                    Bs {{ number_format(optional($don)->monto ?? 0, 2, ',', '.') }}
                                </small>
                            </td>
                            <td>
                                {{ optional($usuario)->nombre }} {{ optional($usuario)->apellido }}<br>
                                <small class="text-muted">
                                    {{ optional($usuario)->email ?? 'Donación anónima' }}
                                </small>
                            </td>
                            <td>
                                {{ optional($campania)->titulo ?? '—' }}<br>
                                <small class="text-muted">
                                    {{ optional($campania)->fechainicio ?? '' }}
                                </small>
                            </td>
                            <td class="text-right">
                                Bs {{ number_format($saldo->montooriginal, 2, ',', '.') }}
                            </td>
                            <td class="text-right">
                                <span class="badge badge-warning">
                                    Bs {{ number_format($saldo->montoutilizado, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <span class="badge badge-success">
                                    Bs {{ number_format($saldo->saldodisponible, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                {{ $saldo->ultimaactualizacion }}
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('saldosdonaciones.edit', $saldo->saldoid) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('saldosdonaciones.destroy', $saldo->saldoid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este registro de saldo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No hay saldos registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
