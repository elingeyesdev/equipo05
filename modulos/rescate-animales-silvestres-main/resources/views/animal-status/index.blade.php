@extends('layouts.app')

@section('title', 'Estados de animal — Rescate')
@section('subtitle', 'Catálogo para el estado clínico o administrativo del animal en custodia.')
@section('content_header_title', 'Estados de animal')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <span id="card_title" class="font-weight-bold mb-0">Registros del catálogo</span>
                            <div>
                                <a href="{{ route('rescate.animal-statuses.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nuevo estado
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4 mb-0">
                            <p class="mb-0">{{ $message }}</p>
                        </div>
                    @endif

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
                                    @forelse ($animalStatuses as $animalStatus)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $animalStatus->nombre }}</td>
                                            <td>
                                                <form action="{{ route('rescate.animal-statuses.destroy', $animalStatus->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.animal-statuses.show', $animalStatus->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.animal-statuses.edit', $animalStatus->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                No hay estados registrados.
                                                <a href="{{ route('rescate.animal-statuses.create') }}" class="d-inline-block mt-2">Crear el primero</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $animalStatuses->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
