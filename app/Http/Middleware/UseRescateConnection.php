<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseRescateConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('rescate');
        DB::reconnect('rescate');

        return $next($request);
    }
}
