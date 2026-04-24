@extends('layouts.app')

@section('title', 'Roles')

@section('header')
    <h1>
        <i class="fas fa-id-badge icon-title"></i>
        Roles del sistema
    </h1>
    <p class="text-muted mb-0">
        Define los distintos niveles de acceso y funciones dentro del sistema de donaciones.
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
        // Spatie usa 'users' para la relación con usuarios
        $totalRoles        = $roles->count();
        $totalAsignaciones = $roles->sum(fn($r) => $r->users->count());
    @endphp

    {{-- Resumen --}}
    <div class="saldo-summary-grid mb-3">
        <div class="saldo-summary-card total-original">
            <h5>TOTAL ROLES</h5>
            <div class="saldo-value">
                {{ $totalRoles }}
            </div>
            <small class="text-muted">
                Cantidad de roles definidos en el sistema.
            </small>
        </div>

        <div class="saldo-summary-card total-utilizado">
            <h5>ASIGNACIONES USUARIO–ROL</h5>
            <div class="saldo-value">
                {{ $totalAsignaciones }}
            </div>
            <small class="text-muted">
                Número total de veces que se asignaron roles a usuarios.
            </small>
        </div>
    </div>

    {{-- Tabla de roles --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de roles
            </h3>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo rol
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center">Usuarios con este rol</th>
                        <th class="text-right" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $rol)
                        <tr>
                            {{-- CAMBIO: Spatie usa 'id' --}}
                            <td>{{ $rol->id }}</td>
                            <td>
                                {{-- CAMBIO: Spatie usa 'name' --}}
                                <span class="role-chip">{{ $rol->name }}</span>
                            </td>
                            <td>
                                {{-- Este campo lo agregamos manual, así que se llama descripcion --}}
                                {{ $rol->descripcion ?? '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">
                                    {{-- CAMBIO: Relación 'users' de Spatie --}}
                                    {{ $rol->users->count() }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('roles.edit', $rol->id) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- CAMBIO: Spatie usa 'id' para borrar --}}
                                    <form action="{{ route('roles.destroy', $rol->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este rol?');">
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
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay roles registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection