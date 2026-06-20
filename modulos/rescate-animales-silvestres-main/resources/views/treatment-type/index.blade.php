@extends('layouts.app')

@section('title', 'Tipos de tratamiento — Rescate')
@section('subtitle', 'Catálogo usado en evaluaciones y registros médicos.')
@section('content_header_title', 'Tipos de tratamiento')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <div>
                                <a href="{{ route('rescate.treatment-types.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo tipo
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
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($treatmentTypes as $treatmentType)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $treatmentType->nombre }}</td>
                                            <td>
                                                <form action="{{ route('rescate.treatment-types.destroy', $treatmentType->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.treatment-types.show', $treatmentType->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.treatment-types.edit', $treatmentType->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                Sin tipos de tratamiento.
                                                <a href="{{ route('rescate.treatment-types.create') }}" class="d-inline-block mt-2">Registrar uno</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $treatmentTypes->withQueryString()->links() !!}
            </div>
        </div>
@endsection
