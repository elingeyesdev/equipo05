<?php

/**
 * Simula dashboardGeneral del modulo inventario (sin HTTP).
 * Uso: php scripts/test-inventario-home.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\Database\YearMonthSql;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionesDinero;
use Modules\Inventario\Models\DonacionDetalle;
use Modules\Inventario\Models\Donante;
use Modules\Inventario\Models\Paquete;
use Modules\Inventario\Models\SolicitudesRecoleccion;

try {
    Donacione::count();
    Paquete::count();
    SolicitudesRecoleccion::where('estado', 'pendiente')->count();

    $ym = YearMonthSql::yearMonthSelect('fecha', 'inventario');
    $gb = YearMonthSql::yearMonthGroupByRaw('fecha', 'inventario');
    Donacione::selectRaw("{$ym}, COUNT(*) as total")
        ->where('fecha', '>=', now()->subMonths(12))
        ->groupByRaw($gb)
        ->get();

    Paquete::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->get();

    DonacionDetalle::join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
        ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id_categoria')
        ->select('categorias_productos.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donacion_detalles.id_detalle) as total'))
        ->groupBy('categorias_productos.nombre')
        ->take(5)
        ->get();

    DonacionesDinero::join('donaciones', 'donaciones_dinero.id_donacion', '=', 'donaciones.id_donacion')
        ->selectRaw(YearMonthSql::yearMonthSelect('donaciones.fecha', 'inventario').', SUM(donaciones_dinero.monto) as total_monto')
        ->where('donaciones.fecha', '>=', now()->subMonths(12))
        ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('donaciones.fecha', 'inventario'))
        ->get();

    Donante::leftJoin('donaciones', 'donantes.id_donante', '=', 'donaciones.id_donante')
        ->select('donantes.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donaciones.id_donacion) as total_donaciones'))
        ->groupBy('donantes.id_donante', 'donantes.nombre')
        ->take(5)
        ->get();

    echo "OK: todas las consultas del dashboard inventario ejecutaron sin error.\n";
    exit(0);
} catch (Throwable $e) {
    echo 'FAIL: '.$e->getMessage()."\n";
    exit(1);
}
