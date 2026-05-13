<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseCuadrillasConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('cuadrillas');
        DB::reconnect('cuadrillas');

        return $next($request);
    }
}
