<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $productos = Producto::paginate();

        return view('inventario::producto.index', compact('productos'))
            ->with('i', ($request->input('page', 1) - 1) * $productos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $producto = new Producto();

        // load categories for FK select
        $categorias = \Modules\Inventario\Models\CategoriasProducto::pluck('nombre', 'id_categoria');

        return view('inventario::producto.create', compact('producto', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'producto' => $producto,
                'message' => 'Producto creado exitosamente'
            ]);
        }

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $producto = Producto::findOrFail($id);

        return view('inventario::producto.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $producto = Producto::find($id);

        // load categories for FK select
        $categorias = \Modules\Inventario\Models\CategoriasProducto::pluck('nombre', 'id_categoria');

        return view('inventario::producto.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Producto::find($id)->delete();

        return Redirect::route('inventario.producto.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}







