<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class UseInventarioConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['database.default' => 'inventario']);
        DB::purge('inventario');
        DB::reconnect('inventario');
        View::getFinder()->prependLocation(base_path('modulos/donacion-recepcion-inventario-main/resources/views'));

        return $next($request);
    }
}
