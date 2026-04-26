@extends('layouts.app')

@section('subtitle', 'Predicciones')
@section('content_header_title', 'Predicciones de Propagación')
@section('content_header_subtitle', '- Listado')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <x-adminlte-alert theme="success" dismissable>
                        {{ $message }}
                    </x-adminlte-alert>
                @endif

                <x-adminlte-card title="Predicciones" theme="purple" icon="fas fa-chart-line">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('predictions.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Generar Predicción
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Foco de Incendio</th>
                                    <th>Fecha de Predicción</th>
                                    <th>Horas</th>
                                    <th>Riesgo</th>
                                    <th>Área Afectada</th>
                                    <th>Puntos</th>
                                    <th style="width: 180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($predictions as $prediction)
                                    @php
                                        $meta = $prediction->meta ?? [];
                                        $riesgo = $meta['fire_risk_index'] ?? 0;
                                        $area = $meta['total_area_affected_km2'] ?? 0;
                                        $horas = $meta['input_parameters']['prediction_hours'] ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            @if($prediction->focoIncendio)
                                                <strong>{{ $prediction->focoIncendio->ubicacion ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $prediction->focoIncendio->fecha?->format('d/m/Y') }}</small>
                                            @else
                                                <strong><i class="fas fa-satellite text-info"></i> Foco FIRMS</strong><br>
                                                <small class="text-muted">{{ $prediction->predicted_at?->format('d/m/Y') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $prediction->predicted_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                                        <td>{{ $horas }}h</td>
                                        <td>
                                            <span class="badge badge-{{ $riesgo > 70 ? 'danger' : ($riesgo > 40 ? 'warning' : 'info') }}">
                                                {{ $riesgo }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($area, 2) }} km²</td>
                                        <td>{{ is_array($prediction->path) ? count($prediction->path) : 0 }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('predictions.show', $prediction->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('predictions.pdf', $prediction->id) }}" class="btn btn-primary btn-sm" title="Ver Informe" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <form action="{{ route('predictions.destroy', $prediction->id) }}" method="POST" style="display: inline;" 
                                                    onsubmit="return confirm('¿Estás seguro de eliminar?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {!! $predictions->withQueryString()->links() !!}
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
