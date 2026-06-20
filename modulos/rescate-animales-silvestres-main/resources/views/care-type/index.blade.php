@extends('layouts.app')

@section('title', 'Tipos de cuidado — Rescate')
@section('subtitle', 'Catálogo para registrar cuidados asociados a hojas de vida.')
@section('content_header_title', 'Tipos de cuidado')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <div>
                                <a href="{{ route('rescate.care-types.create') }}" class="btn btn-success btn-sm">
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
                                        <th>Descripción</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($careTypes as $careType)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $careType->nombre }}</td>
                                            <td>{{ $careType->descripcion ?: '—' }}</td>
                                            <td>
                                                <form action="{{ route('rescate.care-types.destroy', $careType->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.care-types.show', $careType->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.care-types.edit', $careType->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No hay tipos de cuidado.
                                                <a href="{{ route('rescate.care-types.create') }}" class="d-inline-block mt-2">Crear el primero</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $careTypes->withQueryString()->links() !!}
            </div>
        </div>
@endsection
