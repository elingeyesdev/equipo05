<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use App\Models\Ext\ExtCategoriaProducto;
use App\Models\Ext\ExtProducto;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;
use Illuminate\Support\Facades\Http;

class IntegracionExternaController extends Controller
{
    protected string $donacionesUrl;

    public function __construct()
    {
        // ✅ Carga la URL de donaciones desde config
        $this->donacionesUrl = config('services.externos.donaciones_url');
    }

    public function syncCategoriasProductos()
    {
        // Usa la propiedad de clase
        $url = "{$this->donacionesUrl}/api/categorias";
        
        $response = Http::get($url);

        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Error al consumir /api/categorias'], 500);
        }
        
        // ... (resto del código de sincronización de categorías igual) ...
        // Te lo resumo para no hacer la respuesta eterna, pero la lógica interna no cambia, 
        // solo asegúrate de usar $response->json() como tenías.
        
        $json = $response->json();
        // Lógica de bucles updateOrCreate...
        foreach ($json['data'] as $cat) {
            $categoriaLocal = ExtCategoriaProducto::updateOrCreate(
                ['idexterno' => $cat['id_categoria']],
                ['nombre' => $cat['nombre']]
            );
            if (!empty($cat['productos'])) {
                foreach ($cat['productos'] as $prod) {
                    ExtProducto::updateOrCreate(
                        ['idexterno' => $prod['id_producto']],
                        ['categoriaid' => $categoriaLocal->categoriaid, 'nombre' => $prod['nombre'], 'unidad_medida' => $prod['unidad_medida'] ?? null]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Categorías sincronizadas.']);
    }

    public function syncAlmacenes()
    {
        // Usa la propiedad de clase
        $url = "{$this->donacionesUrl}/api/almacenes-completo";

        $response = Http::get($url);

        if (!$response->successful()) {
             return response()->json(['success' => false, 'message' => 'Error al consumir /api/almacenes-completo'], 500);
        }

        $json = $response->json();
        // Lógica de bucles updateOrCreate para Almacenes/Estantes/Espacios...
        foreach ($json['data'] as $alm) {
             $almacenLocal = ExtAlmacen::updateOrCreate(
                ['idexterno' => $alm['id_almacen']],
                ['nombre' => $alm['nombre'], 'direccion' => $alm['direccion'] ?? null]
             );
             // ... bucles de estantes y espacios ...
             if (!empty($alm['estantes'])) {
                 foreach ($alm['estantes'] as $est) {
                     $estanteLocal = ExtEstante::updateOrCreate(
                         ['idexterno' => $est['id_estante']],
                         ['almacenid' => $almacenLocal->almacenid, 'codigo_estante' => $est['codigo_estante']]
                     );
                     if (!empty($est['espacios'])) {
                         foreach ($est['espacios'] as $esp) {
                             ExtEspacio::updateOrCreate(
                                 ['idexterno' => $esp['id_espacio']],
                                 ['estanteid' => $estanteLocal->estanteid, 'codigo_espacio' => $esp['codigo_espacio'], 'estado' => $esp['estado']]
                             );
                         }
                     }
                 }
             }
        }

        return back()->with('success', 'Almacenes sincronizados correctamente.');
    }

    public function syncAll()
    {
        $this->syncCategoriasProductos();
        $this->syncAlmacenes();
        return response()->json(['success' => true, 'message' => 'Sincronización completa.']);
    }
}