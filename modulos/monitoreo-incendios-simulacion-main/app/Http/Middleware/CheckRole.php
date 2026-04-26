<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        \Log::info('CheckRole middleware ejecutándose', [
            'url' => $request->url(),
            'method' => $request->method(),
            'role_required' => $role,
            'user_id' => auth()->id(),
            'all_data' => $request->all()
        ]);
        
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Check role
        switch ($role) {
            case 'administrador':
                if (!$user->isAdministrador()) {
                    abort(403, 'Acceso denegado. Solo administradores pueden acceder a esta sección.');
                }
                break;

            case 'voluntario':
                if (!$user->isVoluntario() && !$user->isAdministrador()) {
                    abort(403, 'Acceso denegado.');
                }
                break;

            default:
                abort(403, 'Rol no válido.');
        }

        \Log::info('CheckRole middleware - Usuario autorizado, continuando...', [
            'user_id' => $user->id,
            'role' => $role
        ]);

        return $next($request);
    }
}
