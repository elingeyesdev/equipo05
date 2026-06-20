@extends('layouts.app')

@section('title', 'Centros — Rescate')
@section('subtitle', 'Centros de custodia y rehabilitación.')
@section('content_header_title', 'Centros')
@section('content_header_subtitle', 'Listado')

@section('content_body')
    
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: .5rem;">
                            <div>
                                <a href="{{ route('rescate.centers.create') }}" class="btn btn-primary btn-sm" data-placement="left">
                                    <i class="fas fa-plus"></i> Nuevo centro
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Dirección</th>
                                        <th>Contacto</th>
                                        <th style="width: 220px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($centers as $center)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $center->nombre }}</td>
                                            <td>{{ $center->direccion }}</td>
                                            <td>{{ $center->contacto }}</td>
                                            <td>
                                                <form action="{{ route('rescate.centers.destroy', $center->id) }}" method="POST" class="d-inline-flex flex-wrap" style="gap: .25rem;">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('rescate.centers.show', $center->id) }}"><i class="fa fa-fw fa-eye"></i> <span class="d-none d-md-inline">Ver</span></a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('rescate.centers.edit', $center->id) }}"><i class="fa fa-fw fa-edit"></i> <span class="d-none d-md-inline">Editar</span></a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm js-confirm-delete"><i class="fa fa-fw fa-trash"></i> <span class="d-none d-md-inline">Eliminar</span></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No hay centros registrados.
                                                <a href="{{ route('rescate.centers.create') }}" class="d-inline-block mt-2">Crear el primero</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $centers->withQueryString()->links() !!}
            </div>
        </div>
@endsection
