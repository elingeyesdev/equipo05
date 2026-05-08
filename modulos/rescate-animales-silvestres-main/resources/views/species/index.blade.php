@extends('layouts.app')

@section('title', 'Especies — Rescate')
@section('subtitle', 'Catálogo taxonómico para reportes y fichas de animales.')
@section('content_header_title', 'Especies')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    <section class="content container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <span id="card_title" class="font-weight-bold mb-0">
                                Especies registradas
                            </span>
                            <div>
                                <a href="{{ route('rescate.species.create') }}" class="btn btn-primary btn-sm" data-placement="left">
                                    <i class="fas fa-plus"></i> Nueva especie
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($species as $item)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $item->nombre }}</td>
                                            <td>
                                                <form action="{{ route('rescate.species.destroy', $item->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.species.show', $item->id) }}"><i class="fa fa-fw fa-eye"></i> <span class="d-none d-md-inline">Ver</span></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('rescate.species.edit', $item->id) }}"><i class="fa fa-fw fa-edit"></i> <span class="d-none d-md-inline">Editar</span></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> <span class="d-none d-md-inline">Eliminar</span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                No hay especies en el catálogo.
                                                <a href="{{ route('rescate.species.create') }}" class="d-inline-block mt-2">Registrar la primera</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $species->withQueryString()->links() !!}
            </div>
        </div>
    </section>
@include('partials.page-pad')
@endsection
