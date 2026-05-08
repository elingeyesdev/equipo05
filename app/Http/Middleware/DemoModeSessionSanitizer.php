<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response;

class DemoModeSessionSanitizer
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isModulePath = $request->is('incendios/modulo') || $request->is('incendios/modulo/*')
            || $request->is('rescate/modulo') || $request->is('rescate/modulo/*');
        if (! $isModulePath || ! $request->hasSession()) {
            return $response;
        }

        $session = $request->session();
        if (! $session->has('errors')) {
            return $response;
        }

        $errors = $session->get('errors');
        if (! $errors instanceof ViewErrorBag) {
            return $response;
        }

        $allMessages = [];
        foreach ($errors->getBags() as $bag) {
            $allMessages = array_merge($allMessages, $bag->all());
        }
        $fullText = strtolower(implode(' | ', $allMessages));

        $looksLikeDbSchemaIssue = str_contains($fullText, 'sqlstate')
            || str_contains($fullText, 'no such table')
            || str_contains($fullText, 'no such column')
            || str_contains($fullText, 'has no column named')
            || str_contains($fullText, 'base table or view not found')
            || str_contains($fullText, 'foreign key constraint failed');

        if (! $looksLikeDbSchemaIssue) {
            return $response;
        }

        $session->forget('errors');
        $successMsg = $this->resolveSuccessMessage($request);

        if ($request->expectsJson()) {
            $session->flash('success', $successMsg);

            return $response;
        }

        $isWriteMethod = in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
        if (! $isWriteMethod) {
            $session->flash('success', $successMsg);

            return $response;
        }

        $targetPath = $this->resolveRedirectPath($request);
        if ($targetPath === null) {
            return redirect()->back()->with('success', $successMsg);
        }

        return redirect($targetPath)->with('success', $successMsg);
    }

    private function resolveSuccessMessage(Request $request): string
    {
        if ($request->is('rescate/modulo/reports/*/approve') || $request->is('incendios/modulo/biomasas/*/aprobar')) {
            return 'Aprobación simulada en modo demo (sin persistencia en base de datos).';
        }

        if ($request->is('incendios/modulo/biomasas/*/rechazar')) {
            return 'Rechazo simulado en modo demo (sin persistencia en base de datos).';
        }

        if ($request->is('rescate/modulo/reports/claim') || $request->is('rescate/modulo/reporte-rapido')) {
            return 'Acción simulada en modo demo (sin persistencia en base de datos).';
        }

        return 'Guardado simulado en modo demo (sin persistencia en base de datos).';
    }

    private function resolveRedirectPath(Request $request): ?string
    {
        // Flujos especiales con destino más natural en demo
        if ($request->is('rescate/modulo/reports/claim')) {
            return '/rescate/modulo/landing';
        }
        if ($request->is('rescate/modulo/reporte-rapido')) {
            return '/rescate/modulo/reporte-rapido';
        }
        if ($request->is('rescate/modulo/reports/*/approve')) {
            return '/rescate/modulo/reports';
        }
        if ($request->is('incendios/modulo/biomasas/*/aprobar') || $request->is('incendios/modulo/biomasas/*/rechazar')) {
            return '/incendios/modulo/biomasas';
        }
        if ($request->is('incendios/modulo/simulaciones/save-simulation')) {
            return '/incendios/modulo/simulaciones';
        }
        if ($request->is('incendios/modulo/focos-incendios/import/firms')) {
            return '/incendios/modulo/focos-incendios';
        }

        $segments = $request->segments();
        if (count($segments) < 3) {
            return null;
        }

        // /{modulo}/modulo/{recurso}[/{id|accion}] -> índice del recurso.
        return '/'.implode('/', array_slice($segments, 0, 3));
    }
}
