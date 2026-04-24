<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use App\Models\Ext\ExtAlmacen;

class AlmacenesEstructuraController extends Controller
{
    public function index()
    {
        // Trae almacenes -> estantes -> espacios -> Y AHORA LOS ITEMS DENTRO
        $almacenes = ExtAlmacen::with(['estantes.espacios.items']) 
            ->orderBy('nombre')
            ->get();

        return view('almacenes.estructura', compact('almacenes'));
    }
}
