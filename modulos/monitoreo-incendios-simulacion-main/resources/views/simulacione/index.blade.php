@extends('layouts.app')

@section('subtitle', 'Simulaciones')
@section('content_header_title', 'Simulaciones')
@section('content_header_subtitle', 'Escenarios de propagación')

@section('content_body')
    @include('incendios::partials.module-nav')
    @include('incendios::partials.flash-messages')

    <div class="card inc-list-card shadow-sm">
        <div class="card-header">
            <div class="inc-btn-toolbar w-100">
                <a href="{{ route('incendios.simulaciones.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Nueva simulación
                </a>
                <a href="{{ route('incendios.simulaciones.simulator') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-fire"></i> Simulador avanzado
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover inc-data-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 4rem;">No</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Focos activos</th>
                            <th style="width: 10rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($simulaciones as $simulacione)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $simulacione->nombre }}</td>
                                <td>{{ optional($simulacione->fecha)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $simulacione->estado === 'activa' ? 'success' : 'secondary' }}">
                                        {{ $simulacione->estado }}
                                    </span>
                                </td>
                                <td><span class="badge badge-danger">{{ $simulacione->focos_activos }}</span></td>
                                <td>
                                    <div class="inc-row-actions">
                                        <a href="{{ route('incendios.simulaciones.show', $simulacione->id) }}" class="btn btn-outline-primary btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('incendios.simulaciones.pdf', $simulacione->id) }}" class="btn btn-outline-secondary btn-sm" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        <a href="{{ route('incendios.simulaciones.edit', $simulacione->id) }}" class="btn btn-outline-secondary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('incendios.simulaciones.destroy', $simulacione->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta simulación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    No hay simulaciones registradas.
                                    <div class="mt-2">
                                        <a href="{{ route('incendios.simulaciones.simulator') }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-fire"></i> Abrir simulador
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($simulaciones->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3">
                {!! $simulaciones->withQueryString()->links() !!}
            </div>
        @endif
    </div>
@endsection
