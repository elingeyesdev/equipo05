@extends('layouts.app')

@section('title', 'Condiciones iniciales — Rescate')
@section('subtitle', 'Catálogo de condiciones del animal al momento del hallazgo.')
@section('content_header_title', 'Condiciones iniciales')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <div>
                                <a href="{{ route('rescate.animal-conditions.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nueva condición
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Severidad</th>
                                        <th>Activo</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($animalConditions as $animalCondition)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $animalCondition->nombre }}</td>
                                            <td>{{ $animalCondition->severidad }}</td>
                                            <td>{{ (int) $animalCondition->activo === 1 ? 'Sí' : 'No' }}</td>
                                            <td>
                                                <form action="{{ route('rescate.animal-conditions.destroy', $animalCondition->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.animal-conditions.show', $animalCondition->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.animal-conditions.edit', $animalCondition->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No hay condiciones registradas.
                                                <a href="{{ route('rescate.animal-conditions.create') }}" class="d-inline-block mt-2">Crear la primera</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $animalConditions->withQueryString()->links() !!}
            </div>
        </div>
@endsection
