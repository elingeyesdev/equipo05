@extends('layouts.app')

@section('subtitle', 'Administradores')
@section('content_header_title', 'Gestión de Administradores')
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

                <x-adminlte-card title="Administradores" theme="warning" icon="fas fa-user-shield">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('administradores.create') }}" class="btn btn-success btn-sm">
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
                                    <th>Departamento</th>
                                    <th>Nivel de Acceso</th>
                                    <th>Activo</th>
                                    <th style="width: 240px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($administradores as $administrador)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $administrador->user->name }}</td>
                                        <td>{{ $administrador->user->email }}</td>
                                        <td>{{ $administrador->departamento }}</td>
                                        <td>{{ $administrador->nivel_acceso }}</td>
                                        <td>
                                            @if($administrador->activo)
                                                <span class="badge badge-success">Sí</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('administradores.show', $administrador->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('administradores.edit', $administrador->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('administradores.destroy', $administrador->id) }}" method="POST" style="display: inline;" 
                                                    onsubmit="return confirm('¿Está seguro de eliminar este administrador?');">
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
                        {!! $administradores->withQueryString()->links() !!}
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
