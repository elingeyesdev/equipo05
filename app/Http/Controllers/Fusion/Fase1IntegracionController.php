<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class Fase1IntegracionController extends Controller
{
    public function index(): View
    {
        $modulos = [
            [
                'nombre' => 'Transparencia de Donaciones',
                'estado' => 'Base activa',
                'descripcion' => 'Frontend y flujo principal heredado del proyecto transparencia.',
            ],
            [
                'nombre' => 'Recepcion e Inventario',
                'estado' => 'En proceso de integracion',
                'descripcion' => 'Se incorporara por etapas: catalogos, donaciones en especie, almacen, paquetes y salidas.',
            ],
        ];

        $fases = [
            'Unificar autenticacion, roles y layout visual.',
            'Mapear tablas y reglas de donaciones/inventario al nuevo esquema.',
            'Incorporar endpoints y pantallas del modulo de inventario.',
            'Validar trazabilidad completa (donacion -> paquete -> salida).',
        ];

        return view('fusion.fase1.index', compact('modulos', 'fases'));
    }
}
