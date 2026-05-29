@props(['seccion', 'id'])

<div class="d-flex flex-wrap logistica-acciones" style="gap:.35rem;">
    <a href="{{ route('logistica.crud.edit', ['seccion' => $seccion, 'id' => $id]) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Modificar
    </a>
    <form action="{{ route('logistica.crud.destroy', ['seccion' => $seccion, 'id' => $id]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="fas fa-trash"></i> Eliminar
        </button>
    </form>
</div>
