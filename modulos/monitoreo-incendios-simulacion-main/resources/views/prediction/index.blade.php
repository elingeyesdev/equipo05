@extends('layouts.app')

@section('subtitle', 'Predicciones')
@section('content_header_title', 'Predicciones de propagación')
@section('content_header_subtitle', 'Análisis por foco')

@section('content_body')
    @include('incendios::partials.module-nav')
    @include('incendios::partials.flash-messages')

    <div class="card inc-list-card shadow-sm">
        <div class="card-header">
            <div class="inc-btn-toolbar w-100">
                <a href="{{ route('incendios.predictions.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Generar predicción
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover inc-data-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 4rem;">No</th>
                            <th>Foco</th>
                            <th>Fecha</th>
                            <th>Horas</th>
                            <th>Riesgo</th>
                            <th>Área afectada</th>
                            <th>Puntos</th>
                            <th style="width: 10rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($predictions as $prediction)
                            @php
                                $meta = $prediction->meta ?? [];
                                $riesgo = $meta['fire_risk_index'] ?? 0;
                                $area = $meta['total_area_affected_km2'] ?? 0;
                                $horas = ($meta['input_parameters'] ?? [])['prediction_hours'] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>
                                    @if($prediction->focoIncendio)
                                        <strong>{{ $prediction->focoIncendio->ubicacion ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $prediction->focoIncendio->fecha?->format('d/m/Y') }}</small>
                                    @else
                                        <strong><i class="fas fa-satellite text-muted"></i> Foco FIRMS</strong>
                                    @endif
                                </td>
                                <td>{{ $prediction->predicted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>{{ $horas }}h</td>
                                <td>
                                    <span class="badge badge-{{ $riesgo > 70 ? 'danger' : ($riesgo > 40 ? 'warning' : 'info') }}">{{ $riesgo }}</span>
                                </td>
                                <td>{{ number_format($area, 2) }} km²</td>
                                <td>{{ count($prediction->normalizedTrajectory()) }}</td>
                                <td>
                                    <div class="inc-row-actions">
                                        <a href="{{ route('incendios.predictions.show', $prediction->id) }}" class="btn btn-outline-primary btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('incendios.predictions.edit', $prediction->id) }}" class="btn btn-outline-secondary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="{{ route('incendios.predictions.pdf', $prediction->id) }}" class="btn btn-outline-secondary btn-sm" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        <form action="{{ route('incendios.predictions.destroy', $prediction->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar predicción?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">No hay predicciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($predictions->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3">
                {!! $predictions->withQueryString()->links() !!}
            </div>
        @endif
    </div>
@endsection
