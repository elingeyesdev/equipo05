@extends('layouts.app')

@section('subtitle', 'Mis Biomasas')
@section('content_header_title', 'Mis Reportes de Biomasa')
@section('content_header_subtitle', 'Gestión Personal')

@section('content_body')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('biomasas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Reportar Nueva Biomasa
                </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <x-adminlte-alert theme="success" dismissable>
                {{ $message }}
            </x-adminlte-alert>
        @endif

        @if ($message = Session::get('info'))
            <x-adminlte-alert theme="info" dismissable>
                {{ $message }}
            </x-adminlte-alert>
        @endif

        <div class="row">
            <div class="col-12">
                <x-adminlte-card title="Mis Biomasas Reportadas" theme="primary" icon="fas fa-leaf">
                    
                    @if($biomasas->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Proceso de Revisión:</strong> Tus biomasas serán revisadas por un administrador antes de aparecer en el mapa del sistema.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Ubicación</th>
                                        <th>Área (m²)</th>
                                        <th>Estado</th>
                                        <th>Fecha Reporte</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($biomasas as $biomasa)
                                        <tr>
                                            <td>{{ $biomasa->id }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'Sin tipo' }}
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                @if(is_array($biomasa->coordenadas) && count($biomasa->coordenadas) > 0)
                                                    {{ number_format($biomasa->coordenadas[0][0] ?? 0, 5) }}, 
                                                    {{ number_format($biomasa->coordenadas[0][1] ?? 0, 5) }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ number_format($biomasa->area_m2, 2) }}</td>
                                            <td>
                                                @if($biomasa->estado == 'pendiente')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </span>
                                                @elseif($biomasa->estado == 'aprobada')
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Aprobada
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Rechazada
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $biomasa->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('biomasas.show', $biomasa->id) }}" 
                                                       class="btn btn-info btn-sm" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($biomasa->estado == 'pendiente')
                                                        <a href="{{ route('biomasas.edit', $biomasa->id) }}" 
                                                           class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('biomasas.destroy', $biomasa->id) }}" 
                                                              method="POST" style="display:inline;" 
                                                              onsubmit="return confirm('¿Está seguro de eliminar esta biomasa?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        @if($biomasa->estado == 'rechazada' && $biomasa->motivo_rechazo)
                                            <tr class="bg-light">
                                                <td colspan="7">
                                                    <div class="alert alert-danger mb-0">
                                                        <strong><i class="fas fa-exclamation-triangle"></i> Motivo de Rechazo:</strong>
                                                        {{ $biomasa->motivo_rechazo }}
                                                        <br>
                                                        <small class="text-muted">
                                                            Rechazada el {{ $biomasa->fecha_revision?->format('d/m/Y H:i') }}
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {!! $biomasas->withQueryString()->links() !!}
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No has reportado ninguna biomasa aún. 
                            <a href="{{ route('biomasas.create') }}" class="alert-link">¡Reporta la primera!</a>
                        </div>
                    @endif
                </x-adminlte-card>

                <!-- Tarjeta informativa -->
                <x-adminlte-card title="Información sobre Estados" theme="secondary" icon="fas fa-question-circle" collapsible>
                    <ul>
                        <li>
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span> - 
                            Tu reporte está en revisión. Puedes editarlo o eliminarlo mientras tanto.
                        </li>
                        <li>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Aprobada</span> - 
                            Tu reporte fue aprobado y ahora aparece en el mapa del sistema.
                        </li>
                        <li>
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rechazada</span> - 
                            Tu reporte fue rechazado. Revisa el motivo y crea un nuevo reporte si es necesario.
                        </li>
                    </ul>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@stop

