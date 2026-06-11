<?php

namespace Modules\Inventario\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Modules\Inventario\Http\Requests\ProductoRequest;
use Modules\Inventario\Models\CategoriasProducto;
use Modules\Inventario\Models\Producto;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::with('categoriaProducto')
            ->withCount('donacionDetalles')
            ->ordenPrioridad()
            ->get();

        $stats = Producto::estadisticasCatalogo();

        $categoriasFiltro = CategoriasProducto::activas()
            ->orderBy('nombre')
            ->pluck('nombre', 'nombre');

        return view('inventario::producto.index', compact('productos', 'stats', 'categoriasFiltro'));
    }

    public function create(): View
    {
        $producto = new Producto();
        $categorias = CategoriasProducto::activas()->orderBy('nombre')->pluck('nombre', 'id_categoria');
        $categoriasMeta = $this->categoriasMetaMap();

        return view('inventario::producto.create', compact('producto', 'categorias', 'categoriasMeta'));
    }

    public function store(ProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'producto' => $producto,
                'message' => 'Producto registrado correctamente',
            ]);
        }

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function show($id): View
    {
        $producto = Producto::with('categoriaProducto')
            ->withCount('donacionDetalles')
            ->findOrFail($id);

        return view('inventario::producto.show', compact('producto'));
    }

    public function edit($id): View
    {
        $producto = Producto::findOrFail($id);
        $categorias = CategoriasProducto::activas()->orderBy('nombre')->pluck('nombre', 'id_categoria');
        $categoriasMeta = $this->categoriasMetaMap();

        return view('inventario::producto.edit', compact('producto', 'categorias', 'categoriasMeta'));
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        $producto = Producto::withCount('donacionDetalles')->findOrFail($id);

        if ($producto->donacion_detalles_count > 0) {
            return Redirect::back()
                ->with('error', 'No se puede eliminar este producto porque ya tiene movimientos o registros asociados. Puede cambiar su estado a inactivo.');
        }

        $producto->delete();

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    private function categoriasMetaMap(): array
    {
        return CategoriasProducto::activas()
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(fn (CategoriasProducto $cat) => [$cat->id_categoria => $cat->toProductoMeta()])
            ->toArray();
    }
}
