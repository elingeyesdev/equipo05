@if($biomasasFiltradas->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Ubicación</th>
                    <th>Área (m²)</th>
                    <th>Fecha Creación</th>
                    @if($estado == 'aprobada')
                        <th>Aprobada Por</th>
                        <th>Fecha Aprobación</th>
                    @endif
                    @if($estado == 'rechazada')
                        <th>Motivo Rechazo</th>
                        <th>Fecha Rechazo</th>
                    @endif
                    @if($estado == 'pendiente')
                        <th>Acciones</th>
                    @else
                        <th>Detalles</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($biomasasFiltradas as $biomasa)
                    <tr>
                        <td>{{ $biomasa->id }}</td>
                        <td>
                            <i class="fas fa-user"></i> 
                            {{ $biomasa->user->name ?? 'N/A' }}
                        </td>
                        <td>
                            <strong>{{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'Sin tipo' }}</strong>
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
                            <small>{{ $biomasa->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        
                        @if($estado == 'aprobada')
                            <td>
                                <i class="fas fa-user-check text-success"></i>
                                {{ $biomasa->aprobadaPor->name ?? 'N/A' }}
                            </td>
                            <td>
                                <small>{{ $biomasa->fecha_revision?->format('d/m/Y H:i') }}</small>
                            </td>
                        @endif

                        @if($estado == 'rechazada')
                            <td>
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ Str::limit($biomasa->motivo_rechazo, 50) }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $biomasa->fecha_revision?->format('d/m/Y H:i') }}</small>
                            </td>
                        @endif

                        <td>
                            @if($estado == 'pendiente')
                                <div class="btn-group" role="group">
                                    <form action="{{ route('biomasas.aprobar', $biomasa->id) }}" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('¿Está seguro de aprobar esta biomasa?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Aprobar">
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                    </form>
                                    
                                    <button onclick="abrirModalRechazo({{ $biomasa->id }})" 
                                            class="btn btn-danger btn-sm ml-1" title="Rechazar">
                                        <i class="fas fa-ban"></i> Rechazar
                                    </button>
                                    
                                    <a href="{{ route('biomasas.show', $biomasa->id) }}" 
                                       class="btn btn-info btn-sm ml-1" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            @else
                                <div class="btn-group" role="group">
                                    <a href="{{ route('biomasas.show', $biomasa->id) }}" 
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    
                                    <form action="{{ route('biomasas.destroy', $biomasa->id) }}" method="POST" 
                                          style="display:inline;" 
                                          onsubmit="return confirm('¿Está seguro de eliminar esta biomasa? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm ml-1" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        No hay biomasas en estado <strong>{{ $estado }}</strong>.
    </div>
@endif
