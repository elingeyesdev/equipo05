<?php

namespace App\Http\Middleware;

use App\Support\AccessControl;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('Administrador')) {
            return $next($request);
        }

        foreach (explode('|', $permissions) as $permission) {
            $permission = trim($permission);
            if ($permission !== '' && $user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'No tiene permiso para realizar esta acción.');
    }
}
