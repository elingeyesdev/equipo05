<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Permite al usuario autenticado fijar un "rol de contexto" por módulo (solo UI / sesión),
 * sin añadir comprobaciones Spatie adicionales en las rutas de los submódulos.
 */
class ModuleViewContextController extends Controller
{
    public function setRescate(Request $request): RedirectResponse
    {
        $request->validate([
            'rol' => 'nullable|string|max:100',
        ]);
        $rol = $request->input('rol');
        if ($rol === null || $rol === '' || $rol === '__default__') {
            $request->session()->forget('modulo_rescate_rol');
        } else {
            $request->session()->put('modulo_rescate_rol', $rol);
        }

        return back();
    }

    public function setIncendios(Request $request): RedirectResponse
    {
        $request->validate([
            'rol' => 'nullable|string|max:100',
        ]);
        $rol = $request->input('rol');
        if ($rol === null || $rol === '' || $rol === '__default__') {
            $request->session()->forget('modulo_incendios_rol');
        } else {
            $request->session()->put('modulo_incendios_rol', $rol);
        }

        return back();
    }
}
