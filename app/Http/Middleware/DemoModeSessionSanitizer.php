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
        $session->flash('success', 'Guardado simulado en modo demo (sin persistencia en base de datos).');

        return $response;
    }
}
