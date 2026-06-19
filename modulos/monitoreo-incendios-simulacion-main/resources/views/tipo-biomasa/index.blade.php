@extends('layouts.app')

@section('subtitle', 'Tipos de Biomasa')
@section('content_header_title', 'Tipos de Biomasa')
@section('content_header_subtitle', 'Catálogo del sistema')

@section('content_body')
    @include('incendios::partials.module-nav')
    @include('incendios::partials.flash-messages')

    <div class="card inc-list-card shadow-sm">
        <div class="card-header">
            <div class="inc-btn-toolbar w-100">
                <a href="{{ route('incendios.tipo-biomasas.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Nuevo tipo
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover inc-data-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 4rem;">No</th>
                            <th>Tipo de biomasa</th>
                            <th>Color</th>
                            <th>Factor propagación</th>
                            <th style="width: 8.5rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipoBiomasas as $tipoBiomasa)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $tipoBiomasa->tipo_biomasa }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $tipoBiomasa->color ?? '#4CAF50' }}; color: #fff;">
                                        {{ $tipoBiomasa->color ?? '#4CAF50' }}
                                    </span>
                                </td>
                                <td><span class="badge badge-light border">{{ $tipoBiomasa->modificador_intensidad ?? 1.0 }}×</span></td>
                                <td>
                                    <div class="inc-row-actions">
                                        <a href="{{ route('incendios.tipo-biomasas.show', $tipoBiomasa->id) }}" class="btn btn-outline-primary btn-sm" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('incendios.tipo-biomasas.edit', $tipoBiomasa->id) }}" class="btn btn-outline-secondary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('incendios.tipo-biomasas.destroy', $tipoBiomasa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tipo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No hay tipos de biomasa registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tipoBiomasas->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3">
                {!! $tipoBiomasas->withQueryString()->links() !!}
            </div>
        @endif
    </div>
@endsection
