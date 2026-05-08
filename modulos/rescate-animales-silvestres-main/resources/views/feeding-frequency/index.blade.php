@extends('layouts.app')

@section('title', 'Frecuencias de alimentación — Rescate')
@section('subtitle', 'Catálogo de frecuencias para planes de alimentación.')
@section('content_header_title', 'Frecuencias de alimentación')
@section('content_header_subtitle', 'Catálogo')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <span id="card_title" class="font-weight-bold mb-0">Frecuencias</span>
                            <div>
                                <a href="{{ route('rescate.feeding-frequencies.create') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Nueva frecuencia
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
                                        <th>Descripción</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($feedingFrequencies as $feedingFrequency)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $feedingFrequency->nombre }}</td>
                                            <td>{{ $feedingFrequency->descripcion ?: '—' }}</td>
                                            <td>
                                                <form action="{{ route('rescate.feeding-frequencies.destroy', $feedingFrequency->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.feeding-frequencies.show', $feedingFrequency->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('rescate.feeding-frequencies.edit', $feedingFrequency->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                Sin frecuencias registradas.
                                                <a href="{{ route('rescate.feeding-frequencies.create') }}" class="d-inline-block mt-2">Crear la primera</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $feedingFrequencies->withQueryString()->links() !!}
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
