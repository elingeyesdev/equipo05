@extends('layouts.app')

@section('subtitle', 'Voluntarios')
@section('content_header_title', 'Gestión de Voluntarios')
@section('content_header_subtitle', '- Listado')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <x-adminlte-alert theme="success" dismissable>
                        {{ $message }}
                    </x-adminlte-alert>
                @endif

                <x-adminlte-card title="Voluntarios" theme="info" icon="fas fa-hands-helping">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('voluntarios.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Crear Nuevo
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Ciudad</th>
                                    <th>Zona</th>
                                    <th>Dirección</th>
                                    <th style="width: 240px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($voluntarios as $voluntario)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $voluntario->user->name }}</td>
                                        <td>{{ $voluntario->user->email }}</td>
                                        <td>{{ $voluntario->ciudad }}</td>
                                        <td>{{ $voluntario->zona }}</td>
                                        <td>{{ $voluntario->direccion }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('voluntarios.show', $voluntario->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('voluntarios.edit', $voluntario->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('voluntarios.destroy', $voluntario->id) }}" method="POST" style="display: inline;" 
                                                    onsubmit="return confirm('¿Está seguro de eliminar este voluntario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {!! $voluntarios->withQueryString()->links() !!}
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
