<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\UbicacionesDonacione;
use Modules\Inventario\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    /**
     * GET /api/inventario/stock
     */
    public function getStock()
    {
        try {
            $stock = Producto::select('productos.*')
                ->selectRaw('COALESCE(SUM(ubicaciones_donaciones.cantidad_ubicada), 0) as stock_total')
                ->leftJoin('donacion_detalles', 'productos.id_producto', '=', 'donacion_detalles.id_producto')
                ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
                ->groupBy('productos.id_producto', 'productos.nombre', 'productos.descripcion', 'productos.categoria', 'productos.unidad_medida', 'productos.created_at', 'productos.updated_at')
                ->get();

            return response()->json($stock, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener stock general'], 500);
        }
    }

    /**
     * GET /api/inventario/stock/articulo/{id}
     */
    public function getStockByArticulo($idProducto)
    {
        try {
            $stock = Producto::select('productos.*')
                ->selectRaw('COALESCE(SUM(ubicaciones_donaciones.cantidad_ubicada), 0) as stock_total')
                ->leftJoin('donacion_detalles', 'productos.id_producto', '=', 'donacion_detalles.id_producto')
                ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
                ->where('productos.id_producto', $idProducto)
                ->groupBy('productos.id_producto', 'productos.nombre', 'productos.descripcion', 'productos.categoria', 'productos.unidad_medida', 'productos.created_at', 'productos.updated_at')
                ->first();

            return response()->json($stock, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener stock del artículo'], 500);
        }
    }

    /**
     * GET /api/inventario/stock/estante/{id}
     */
    public function getStockByEstante($idEstante)
    {
        try {
            $stock = Producto::select('productos.*')
                ->selectRaw('COALESCE(SUM(ubicaciones_donaciones.cantidad_ubicada), 0) as stock_total')
                ->leftJoin('donacion_detalles', 'productos.id_producto', '=', 'donacion_detalles.id_producto')
                ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
                ->leftJoin('espacios', 'ubicaciones_donaciones.id_espacio', '=', 'espacios.id_espacio')
                ->where('espacios.id_estante', $idEstante)
                ->groupBy('productos.id_producto', 'productos.nombre', 'productos.descripcion', 'productos.categoria', 'productos.unidad_medida', 'productos.created_at', 'productos.updated_at')
                ->get();

            return response()->json($stock, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener stock del estante'], 500);
        }
    }

    /**
     * GET /api/inventario/por-producto
     * Retorna todo el inventario agrupado y ordenado por producto
     */
    public function getInventoryByProduct()
    {
        try {
            $inventario = DB::connection('inventario')->table('ubicaciones_donaciones')
                ->join('donacion_detalles', 'ubicaciones_donaciones.id_detalle', '=', 'donacion_detalles.id_detalle')
                ->join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
                ->select(
                    'productos.id_producto',
                    'productos.nombre',
                    'productos.descripcion',
                    'productos.id_categoria',
                    'productos.unidad_medida',
                    DB::raw('COALESCE(SUM(ubicaciones_donaciones.cantidad_ubicada), 0) as stock_total')
                )
                ->groupBy(
                    'productos.id_producto',
                    'productos.nombre',
                    'productos.descripcion',
                    'productos.id_categoria',
                    'productos.unidad_medida'
                )
                ->orderBy('productos.nombre', 'asc')
                ->get();

            return response()->json($inventario, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener inventario por producto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return UbicacionesDonacione::with(['detalle.producto', 'espacio.estante'])->get();
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}







