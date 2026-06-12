<?php

namespace App\Http\Middleware;

use App\Support\AccessControl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleAccess
{
    /**
     * @param  string  $module  Clave: admin|inventario|incendios|logistica|seguimiento|cuadrillas|rescate
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Debe iniciar sesión para acceder a este módulo.');
        }

        if (! AccessControl::userCanAccessModule($user, $module)) {
            abort(403, 'No tiene permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
