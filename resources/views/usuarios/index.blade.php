@extends('layouts.app')

@section('title', 'Usuarios')

@section('header')
    <h1>
        <i class="fas fa-users icon-title"></i>
        Usuarios
    </h1>
    <p class="text-muted mb-0">
        Gestión de usuarios y sus roles dentro del sistema de donaciones.
    </p>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $totalUsuarios = $usuarios->count();
        $activos       = $usuarios->where('activo', 1)->count();
    @endphp

    {{-- Resumen rápido --}}
    <div class="saldo-summary-grid mb-3">
        <div class="saldo-summary-card total-original">
            <h5>USUARIOS REGISTRADOS</h5>
            <div class="saldo-value">
                {{ $totalUsuarios }}
            </div>
            <small class="text-muted">Total de usuarios en el sistema.</small>
        </div>
        <div class="saldo-summary-card total-utilizado">
            <h5>USUARIOS ACTIVOS</h5>
            <div class="saldo-value">
                {{ $activos }}
            </div>
            <small class="text-muted">Usuarios habilitados actualmente.</small>
        </div>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de usuarios
            </h3>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo usuario
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th class="text-right" style="width: 180px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="user-avatar-circle bg-info text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ strtoupper(substr($usuario->nombre,0,1)) }}{{ strtoupper(substr($usuario->apellido,0,1)) }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ $usuario->nombre }} {{ $usuario->apellido }}</strong>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->telefono ?? '—' }}</td>
                            <td>
                                {{-- Iteramos los roles de Spatie --}}
                                @forelse($usuario->roles as $rol)
                                    <span class="badge badge-info text-capitalize">{{ $rol->name }}</span>
                                @empty
                                    <span class="text-muted text-sm">Sin rol</span>
                                @endforelse
                            </td>
                            <td>
                                @if($usuario->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{ $usuario->fecharegistro ? \Carbon\Carbon::parse($usuario->fecharegistro)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- Estado de cuenta --}}
                                    {{-- Verifica si tienes definida esta ruta en web.php, si no, coméntala --}}
                                    {{-- <a href="{{ route('usuarios.estadoCuenta', $usuario->usuarioid) }}"
                                       class="btn btn-outline-info" title="Estado de cuenta">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a> --}}

                                    <a href="{{ route('usuarios.edit', $usuario->usuarioid) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('usuarios.destroy', $usuario->usuarioid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este usuario?');"
                                          style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No hay usuarios registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection