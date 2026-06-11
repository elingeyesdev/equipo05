<?php

namespace Modules\Inventario\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Modules\Inventario\Http\Requests\CategoriasProductoRequest;
use Modules\Inventario\Models\CategoriaProductoHistorial;
use Modules\Inventario\Models\CategoriasProducto;

class CategoriasProductoController extends Controller
{
    public function index(Request $request): View
    {
        $categoriasProductos = CategoriasProducto::withCount('productos')
            ->ordenEmergencia()
            ->get();

        $puedeGestionar = $this->esAdministrador();

        return view('inventario::categorias-producto.index', compact('categoriasProductos', 'puedeGestionar'));
    }

    public function create(): View
    {
        $this->abortSiNoEsAdministrador();

        $categoriasProducto = new CategoriasProducto();

        return view('inventario::categorias-producto.create', compact('categoriasProducto'));
    }

    public function store(CategoriasProductoRequest $request)
    {
        $categoria = CategoriasProducto::create(array_merge($request->validated(), ['estado' => 'activo']));
        $this->registrarHistorial($categoria, 'creado', null, $categoria->toArray());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'categoria' => $categoria,
                'message' => 'Categoría registrada exitosamente',
            ]);
        }

        return Redirect::route('inventario.categorias-producto.index')
            ->with('success', 'Categoría de donación registrada exitosamente.');
    }

    public function show($id): View
    {
        $categoriasProducto = CategoriasProducto::with(['historial' => fn ($q) => $q->limit(20)])
            ->withCount('productos')
            ->findOrFail($id);

        $puedeGestionar = $this->esAdministrador();

        return view('inventario::categorias-producto.show', compact('categoriasProducto', 'puedeGestionar'));
    }

    public function edit($id): View
    {
        $this->abortSiNoEsAdministrador();

        $categoriasProducto = CategoriasProducto::findOrFail($id);

        return view('inventario::categorias-producto.edit', compact('categoriasProducto'));
    }

    public function update(CategoriasProductoRequest $request, CategoriasProducto $categoriasProducto): RedirectResponse
    {
        $antes = $categoriasProducto->toArray();
        $categoriasProducto->update($request->validated());
        $this->registrarHistorial($categoriasProducto, 'actualizado', $antes, $categoriasProducto->fresh()->toArray());

        return Redirect::route('inventario.categorias-producto.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->abortSiNoEsAdministrador();

        $categoria = CategoriasProducto::withCount('productos')->findOrFail($id);

        if ($categoria->productos_count > 0) {
            return Redirect::back()
                ->with('error', 'No se puede eliminar: hay productos asociados a esta categoría.');
        }

        $this->registrarHistorial($categoria, 'eliminado', $categoria->toArray(), null);
        $categoria->delete();

        return Redirect::route('inventario.categorias-producto.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    private function esAdministrador(): bool
    {
        return auth()->user()?->hasRole('Administrador') ?? false;
    }

    private function abortSiNoEsAdministrador(): void
    {
        if (! $this->esAdministrador()) {
            abort(403, 'Solo administradores pueden gestionar categorías de donación.');
        }
    }

    private function registrarHistorial(
        CategoriasProducto $categoria,
        string $accion,
        ?array $antes,
        ?array $despues
    ): void {
        CategoriaProductoHistorial::create([
            'id_categoria' => $categoria->id_categoria,
            'accion' => $accion,
            'usuario_ci' => auth()->user()->ci ?? auth()->user()->email ?? null,
            'datos_anteriores' => $antes,
            'datos_nuevos' => $despues,
            'created_at' => now(),
        ]);
    }
}
