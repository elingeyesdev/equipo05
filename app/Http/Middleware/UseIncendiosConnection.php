<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseIncendiosConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['database.default' => 'incendios']);
        DB::purge('incendios');
        DB::reconnect('incendios');

        return $next($request);
    }
}
