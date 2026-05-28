<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUtf8Response
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $contentType = $response->headers->get('Content-Type', '');

        if (str_contains($contentType, 'text/html') && ! str_contains($contentType, 'charset')) {
            $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        }

        return $response;
    }
}
