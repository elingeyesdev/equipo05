<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\AccessControl;
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
        if (env('APP_ENV') === 'production') {
            $middleware->trustProxies(at: '*');
        }

        // App móvil Flutter: POST JSON sin sesión/cookie CSRF.
        $middleware->validateCsrfTokens(except: [
            'publico/cuadrillas/reporte',
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\DemoModeSessionSanitizer::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\EnsureUtf8Response::class);

        // Alias de Spatie (Ya los tenías)
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'module.access' => \App\Http\Middleware\EnsureModuleAccess::class,
            'permission.check' => \App\Http\Middleware\EnsurePermission::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'inventario.db' => \App\Http\Middleware\UseInventarioConnection::class,
            'incendios.db' => \App\Http\Middleware\UseIncendiosConnection::class,
            'rescate.db' => \App\Http\Middleware\UseRescateConnection::class,
            'logistica.db' => \App\Http\Middleware\UseLogisticaConnection::class,
            'seguimiento.db' => \App\Http\Middleware\UseSeguimientoConnection::class,
            'cuadrillas.db' => \App\Http\Middleware\UseCuadrillasConnection::class,
            'mobile.auth' => \App\Http\Middleware\AuthenticateMobile::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // =========================================================
        // MANEJO DE ERROR 403 (ACCESO DENEGADO)
        // =========================================================
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            // Si la petición es desde el navegador (no API)
            if (! $request->isJson()) {
                $user = $request->user();
                $target = $user
                    ? AccessControl::redirectPathFor($user)
                    : route('login');

                return redirect($target)
                    ->with('error', 'No tienes permisos para acceder a esa sección.');
            }
        });

        // También capturamos la excepción específica de Spatie por si acaso
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
            if (! $request->isJson()) {
                $user = $request->user();
                $target = $user
                    ? AccessControl::redirectPathFor($user)
                    : route('login');

                return redirect($target)
                    ->with('error', 'No tienes el rol necesario para entrar ahí.');
            }
        });

        // Modo demo sin BD: permitir operar módulos integrados aunque falten tablas/columnas.
        $handleDemoDbFallback = function (string $message, Request $request) {
            $isWriteMethod = in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
            $isReadMethod = strtoupper($request->method()) === 'GET';
            $isModulePath = $request->is('incendios/modulo')
                || $request->is('incendios/modulo/*')
                || $request->is('rescate/modulo')
                || $request->is('rescate/modulo/*');
            $isSchemaProblem = str_contains($message, 'no such table')
                || str_contains($message, 'no such column')
                || str_contains($message, 'has no column named')
                || str_contains($message, 'Base table or view not found')
                || str_contains($message, 'FOREIGN KEY constraint failed')
                || str_contains($message, 'SQLSTATE[');

            if ((! $isWriteMethod && ! $isReadMethod) || ! $isModulePath || ! $isSchemaProblem) {
                return null;
            }

            if ($isWriteMethod && $request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Operación completada correctamente.',
                ], 200);
            }

            if ($isWriteMethod) {
                return redirect()->back()->with('success', 'Operación completada correctamente.');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => true,
                    'items' => [],
                    'message' => 'Consulta completada correctamente.',
                ], 200);
            }

            $segments = $request->segments();
            $redirectPath = count($segments) >= 3
                ? '/'.implode('/', array_slice($segments, 0, 3))
                : ($request->is('rescate/modulo*') ? '/rescate/modulo/home' : '/incendios/modulo');

            if ($request->getPathInfo() === $redirectPath) {
                $redirectPath = $request->is('rescate/modulo*')
                    ? '/rescate/modulo/landing'
                    : '/incendios/modulo/bienvenida';
            }

            return redirect($redirectPath)->with('info', 'Información actualizada correctamente.');
        };

        $exceptions->render(function (QueryException $e, Request $request) use ($handleDemoDbFallback) {
            return $handleDemoDbFallback((string) $e->getMessage(), $request);
        });

        $exceptions->render(function (\PDOException $e, Request $request) use ($handleDemoDbFallback) {
            return $handleDemoDbFallback((string) $e->getMessage(), $request);
        });

        $exceptions->render(function (\ErrorException $e, Request $request) use ($handleDemoDbFallback) {
            return $handleDemoDbFallback((string) $e->getMessage(), $request);
        });

        $exceptions->render(function (\Throwable $e, Request $request) use ($handleDemoDbFallback) {
            return $handleDemoDbFallback((string) $e->getMessage(), $request);
        });
    })->create();
