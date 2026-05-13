<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseSeguimientoConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('seguimiento');
        DB::reconnect('seguimiento');

        return $next($request);
    }
}
