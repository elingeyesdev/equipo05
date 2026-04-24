@extends('layouts.app')

@section('title', 'Donaciones')

@section('header')
    <h1>
        <i class="fas fa-hand-holding-heart icon-title"></i>
        Donaciones
    </h1>
    <p class="text-muted mb-0">
        Registro de todas las donaciones y su estado actual.
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
        $totalDonado = $donaciones->sum('monto');
        $totalMonetaria = $donaciones->filter(function($d){
            return \Illuminate\Support\Str::lower($d->tipodonacion) === 'monetaria';
        })->sum('monto');
        $totalEspecie = $donaciones->filter(function($d){
            return \Illuminate\Support\Str::lower($d->tipodonacion) === 'especie';
        })->sum('monto');
    @endphp

    {{-- RESUMEN --}}
    <div class="donaciones-summary-grid">
        <div class="donaciones-summary-card total-donado">
            <h5>TOTAL DONADO</h5>
            <div class="summary-value">
                Bs {{ number_format($totalDonado, 2, ',', '.') }}
            </div>
            <small class="text-muted">Suma de todas las donaciones registradas</small>
        </div>
        <div class="donaciones-summary-card total-monetaria">
            <h5>DONACIONES MONETARIAS</h5>
            <div class="summary-value">
                Bs {{ number_format($totalMonetaria, 2, ',', '.') }}
            </div>
            <small class="text-muted">Monto total en efectivo</small>
        </div>
        <div class="donaciones-summary-card total-especie">
            <h5>DONACIONES EN ESPECIE</h5>
            <div class="summary-value">
                Bs {{ number_format($totalEspecie, 2, ',', '.') }}
            </div>
            <small class="text-muted">Valor estimado de donaciones en especie</small>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de donaciones
            </h3>
            <a href="{{ route('donaciones.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nueva donación
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Donante</th>
                        <th>Campaña</th>
                        <th>Tipo</th>
                        <th class="text-right">Monto</th>
                        <th>Estado</th>
                        <th>Saldo disp.</th>
                        <th>Fecha</th>
                        <th class="text-right" style="width: 190px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donaciones as $donacion)
                        @php
                            $usuario  = optional($donacion->usuario);
                            $campania = optional($donacion->campania);
                            $estado   = optional($donacion->estado);
                            $saldo    = optional($donacion->saldo);
                        @endphp
                        <tr>
                            <td>{{ $donacion->donacionid }}</td>
                            <td>
                                @if($donacion->esanonima)
                                    <strong>Donación anónima</strong>
                                @else
                                    {{ $usuario->nombre }} {{ $usuario->apellido }}<br>
                                    <small class="text-muted">{{ $usuario->email }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $campania->titulo ?? '—' }}<br>
                                <small class="text-muted">
                                    {{ $campania->fechainicio ?? '' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $donacion->tipodonacion }}</span>
                            </td>
                            <td class="text-right">
                                <span class="amount-positive">
                                    Bs {{ number_format($donacion->monto, 2, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $estado->nombre ?? '—' }}
                                </span>
                            </td>
                            <td class="text-right">
                                @if($saldo)
                                    <span class="badge badge-success">
                                        Bs {{ number_format($saldo->saldodisponible, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge badge-light">—</span>
                                @endif
                            </td>
                            <td>
                                {{ $donacion->fechadonacion }}
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    {{-- reasignar campaña --}}
                                    <a href="{{ route('donaciones.edit', $donacion->donacionid) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('donaciones.destroy', $donacion->donacionid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar esta donación?');">
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
                                No hay donaciones registradas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
