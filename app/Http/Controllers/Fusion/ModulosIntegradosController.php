<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;

class ModulosIntegradosController extends Controller
{
    public function incendios()
    {
        return view('fusion.modulos.incendios');
    }

    public function rescate()
    {
        return view('fusion.modulos.rescate');
    }
}

