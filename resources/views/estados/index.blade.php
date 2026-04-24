@extends('layouts.app')

@section('title', 'Estados')

@section('header')
    <h1>
        <i class="fas fa-flag icon-title"></i>
        Estados de donaciones
    </h1>
    <p class="text-muted mb-0">
        Lista de estados que se utilizan para clasificar las donaciones (Pendiente, Confirmada, etc.).
    </p>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-list mr-1"></i> Listado de estados
            </h3>
            <a href="{{ route('estados.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Nuevo estado
            </a>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-right" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($estados as $estado)
                        @php
                            $nombre = \Illuminate\Support\Str::lower($estado->nombre);
                            $badgeClass = 'badge-secondary';

                            if (str_contains($nombre, 'pendiente'))   $badgeClass = 'badge-warning';
                            if (str_contains($nombre, 'confirmada')) $badgeClass = 'badge-success';
                            if (str_contains($nombre, 'asignada'))   $badgeClass = 'badge-info';
                            if (str_contains($nombre, 'utilizada'))  $badgeClass = 'badge-primary';
                            if (str_contains($nombre, 'cancelada'))  $badgeClass = 'badge-danger';
                        @endphp
                        <tr>
                            <td>{{ $estado->estadoid }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }}">
                                    {{ $estado->nombre }}
                                </span>
                            </td>
                            <td>
                                {{ $estado->descripcion ?? '—' }}
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('estados.edit', $estado->estadoid) }}"
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('estados.destroy', $estado->estadoid) }}"
                                          method="POST"
                                          onsubmit="return confirm('¿Eliminar este estado?');">
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
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay estados registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($estados, 'links'))
            <div class="card-footer">
                {{ $estados->links() }}
            </div>
        @endif
    </div>
@endsection
