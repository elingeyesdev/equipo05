<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAllRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'POST') {
            \Log::info('POST REQUEST DETECTADO', [
                'url' => $request->url(),
                'route' => $request->route() ? $request->route()->getName() : 'NO ROUTE',
                'all_data' => $request->all(),
                'user_id' => auth()->id()
            ]);
        }

        return $next($request);
    }
}
