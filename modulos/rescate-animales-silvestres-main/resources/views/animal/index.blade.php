@extends('layouts.app')

@section('title', 'Animales — Rescate')
@section('subtitle', 'Registro de animales atendidos por el módulo.')
@section('content_header_title', 'Animales')
@section('content_header_subtitle', 'Listado')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card res-list-card res-accent-success">
                    <div class="card-header res-card-header--actions-only">
                        <div class="res-card-header-actions">
                                <a href="{{ route('rescate.animals.create') }}" class="btn btn-primary btn-sm" data-placement="left">
                                  <i class="fas fa-plus"></i> Nuevo animal
                                </a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        
									<th >Nombre</th>
									<th >Sexo</th>
									<th >Descripcion</th>
									<th >Número de reporte</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($animals as $animal)
                                        <tr>
                                            <td>{{ ++$i }}</td>

										<td>{{ $animal->nombre }}</td>
										<td>{{ $animal->sexo }}</td>
										<td>{{ $animal->descripcion }}</td>
										<td>{{ $animal->reporte_id ? '#' . $animal->reporte_id : '—' }}</td>

                                            <td>
                                                <form action="{{ route('rescate.animals.destroy', $animal->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.animals.show', $animal->id) }}"><i class="fa fa-fw fa-eye"></i> Ver</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('rescate.animals.edit', $animal->id) }}"><i class="fa fa-fw fa-edit"></i> Editar</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No hay animales registrados todavía.
                                                <a href="{{ route('rescate.animals.create') }}" class="d-inline-block mt-2">Crear el primero</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($animals->hasPages())
                        <div class="card-footer d-flex justify-content-center">
                            {!! $animals->withQueryString()->links('pagination::bootstrap-4') !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
@endsection

