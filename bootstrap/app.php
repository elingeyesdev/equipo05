<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias de Spatie (Ya los tenías)
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'inventario.db' => \App\Http\Middleware\UseInventarioConnection::class,
            'incendios.db' => \App\Http\Middleware\UseIncendiosConnection::class,
            'rescate.db' => \App\Http\Middleware\UseRescateConnection::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // =========================================================
        // MANEJO DE ERROR 403 (ACCESO DENEGADO)
        // =========================================================
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            // Si la petición es desde el navegador (no API)
            if (! $request->isJson()) {
                // Redirigir al dashboard con mensaje de error
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes permisos para acceder a esa sección.');
            }
        });
        
        // También capturamos la excepción específica de Spatie por si acaso
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
             if (! $request->isJson()) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes el rol necesario para entrar ahí.');
            }
        });
    })->create();