<?php

use App\Models\Almacene;
use App\Models\UbicacionesDonacione;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find an almacen to test (or create one)
$almacen = Almacene::find(2);

if (!$almacen) {
    echo "No hay almacenes para probar.\n";
    exit;
}

echo "Intentando eliminar almacén ID: " . $almacen->id_almacen . "\n";

DB::beginTransaction();
try {
    // Logic from controller
    foreach ($almacen->estantes as $estante) {
        echo "Procesando estante ID: " . $estante->id_estante . "\n";
        foreach ($estante->espacios as $espacio) {
            echo "  Procesando espacio ID: " . $espacio->id_espacio . "\n";
            $deleted = UbicacionesDonacione::where('id_espacio', $espacio->id_espacio)->delete();
            echo "    Ubicaciones eliminadas: " . $deleted . "\n";
        }
        $estante->espacios()->delete();
        echo "  Espacios eliminados.\n";
    }

    $almacen->estantes()->delete();
    echo "Estantes eliminados.\n";

    $almacen->delete();
    echo "Almacén eliminado.\n";

    DB::rollBack(); // Don't actually delete it
    echo "Test completado exitosamente (Rollback realizado).\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}



