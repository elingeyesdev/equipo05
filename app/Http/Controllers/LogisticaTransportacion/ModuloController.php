<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        return view('fusion.modulos.logistica');
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'logistica-transportacion-donaciones',
        ]);
    }
}
