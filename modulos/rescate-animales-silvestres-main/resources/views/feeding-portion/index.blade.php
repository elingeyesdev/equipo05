@extends('layouts.app')

@section('title', 'Porciones de alimentación — Rescate')
@section('subtitle', 'Catálogo de cantidades y unidades para alimentación.')
@section('content_header_title', 'Porciones de alimentación')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <span id="card_title" class="font-weight-bold mb-0">Porciones</span>
                            <div>
                                <a href="{{ route('rescate.feeding-portions.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nueva porción
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
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($feedingPortions as $feedingPortion)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $feedingPortion->cantidad }}</td>
                                            <td>{{ $feedingPortion->unidad }}</td>
                                            <td>
                                                <form action="{{ route('rescate.feeding-portions.destroy', $feedingPortion->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.feeding-portions.show', $feedingPortion->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.feeding-portions.edit', $feedingPortion->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                Sin porciones registradas.
                                                <a href="{{ route('rescate.feeding-portions.create') }}" class="d-inline-block mt-2">Crear la primera</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $feedingPortions->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
