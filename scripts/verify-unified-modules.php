<?php

/**
 * Verifica conexiones PG unificadas, conteos y consultas criticas por modulo.
 * Uso: php scripts/verify-unified-modules.php
 */

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ok = true;
$report = [];

function check(string $label, callable $fn): void
{
    global $ok, $report;
    try {
        $fn();
        $report[] = ['ok', $label];
    } catch (Throwable $e) {
        $ok = false;
        $report[] = ['fail', $label.' => '.$e->getMessage()];
    }
}

if (! filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL)) {
    echo "AVISO: DATABASE_UNIFIED_POSTGRES no esta en true.\n";
}

$connections = ['inventario', 'incendios', 'rescate', 'logistica', 'seguimiento', 'cuadrillas'];

foreach ($connections as $conn) {
    check("{$conn}: driver pgsql", function () use ($conn) {
        $d = Illuminate\Support\Facades\DB::connection($conn)->getDriverName();
        if ($d !== 'pgsql') {
            throw new RuntimeException("driver={$d}");
        }
    });
}

check('inventario: Donacione::count + soft deletes', function () {
    $n = Modules\Inventario\Models\Donacione::count();
    if ($n < 1) {
        throw new RuntimeException("sin donaciones (count={$n})");
    }
});

check('inventario: dashboardGeneral queries', function () {
    config(['database.default' => 'inventario']);
  Illuminate\Support\Facades\DB::purge('inventario');
  Illuminate\Support\Facades\DB::reconnect('inventario');
    $ym = App\Support\Database\YearMonthSql::yearMonthSelect('fecha', 'inventario');
    $gb = App\Support\Database\YearMonthSql::yearMonthGroupByRaw('fecha', 'inventario');
    Modules\Inventario\Models\Donacione::selectRaw("{$ym}, COUNT(*) as total")
        ->where('fecha', '>=', now()->subMonths(12))
        ->groupByRaw($gb)
        ->get();
    Modules\Inventario\Models\Paquete::count();
    Modules\Inventario\Models\SolicitudesRecoleccion::where('estado', 'pendiente')->count();
});

check('incendios: tipo_biomasa tiene datos', function () {
    $n = Illuminate\Support\Facades\DB::connection('incendios')->table('tipo_biomasa')->count();
    if ($n < 1) {
        throw new RuntimeException("tipo_biomasa vacio");
    }
});

check('rescate: species tiene datos', function () {
    $n = Illuminate\Support\Facades\DB::connection('rescate')->table('species')->count();
    if ($n < 1) {
        throw new RuntimeException('species vacio');
    }
});

check('logistica: estado tiene datos', function () {
    $n = Illuminate\Support\Facades\DB::connection('logistica')->table('estado')->count();
    if ($n < 1) {
        throw new RuntimeException('estado vacio');
    }
});

check('seguimiento: usuario tiene datos', function () {
    $n = Illuminate\Support\Facades\DB::connection('seguimiento')->table('usuario')->count();
    if ($n < 1) {
        throw new RuntimeException('usuario vacio');
    }
});

check('cuadrillas: curso tiene datos', function () {
    $n = Illuminate\Support\Facades\DB::connection('cuadrillas')->table('curso')->count();
    if ($n < 1) {
        throw new RuntimeException('curso vacio');
    }
});

echo "\n=== Verificacion base unificada ===\n";
foreach ($report as [$status, $msg]) {
    echo ($status === 'ok' ? '[OK] ' : '[FAIL] ').$msg."\n";
}

echo "\nConteos por esquema:\n";
foreach ($connections as $conn) {
    try {
        $tables = match ($conn) {
            'inventario' => ['donaciones', 'donantes', 'paquetes', 'campanas'],
            'incendios' => ['tipo_biomasa', 'biomasas'],
            'rescate' => ['species', 'animals'],
            'logistica' => ['estado', 'solicitud'],
            'seguimiento' => ['usuario', 'capacitacion'],
            'cuadrillas' => ['curso', 'comunario'],
            default => [],
        };
        $parts = [];
        foreach ($tables as $t) {
            if (Illuminate\Support\Facades\Schema::connection($conn)->hasTable($t)) {
                $c = Illuminate\Support\Facades\DB::connection($conn)->table($t)->count();
                $parts[] = "{$t}={$c}";
            }
        }
        echo "  {$conn}: ".implode(', ', $parts)."\n";
    } catch (Throwable $e) {
        echo "  {$conn}: ERROR ".$e->getMessage()."\n";
    }
}

exit($ok ? 0 : 1);
