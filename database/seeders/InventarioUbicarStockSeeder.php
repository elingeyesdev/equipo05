<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ubica donaciones en espacios de almacén y marca ocupación realista para el dashboard.
 */
class InventarioUbicarStockSeeder extends Seeder
{
    /** @var array<string, float> Porcentaje objetivo de espacios llenos por almacén */
    private array $ocupacionObjetivo = [
        'Punto Plan 3000' => 0.67,
        'Depósito Norte' => 0.75,
        'Central SCZ' => 0.67,
        'Bodega Cotoca' => 0.58,
    ];

    public function run(): void
    {
        if (! Schema::connection('inventario')->hasTable('ubicaciones_donaciones')) {
            return;
        }

        $db = DB::connection('inventario');

        $detallesPendientes = $db->table('donacion_detalles')
            ->whereNotIn('id_detalle', $db->table('ubicaciones_donaciones')->pluck('id_detalle'))
            ->orderBy('id_detalle')
            ->get();

        if ($detallesPendientes->isEmpty() && $db->table('espacios')->whereRaw('LOWER(COALESCE(estado, \'disponible\')) = ?', ['lleno'])->exists()) {
            $this->command?->info('Inventario: stock ya ubicado en almacén.');

            return;
        }

        $espaciosPorAlmacen = $db->table('almacenes')
            ->join('estantes', 'almacenes.id_almacen', '=', 'estantes.id_almacen')
            ->join('espacios', 'estantes.id_estante', '=', 'espacios.id_estante')
            ->select('almacenes.nombre as almacen', 'espacios.id_espacio')
            ->orderBy('almacenes.id_almacen')
            ->orderBy('estantes.id_estante')
            ->orderBy('espacios.id_espacio')
            ->get()
            ->groupBy('almacen');

        $espaciosObjetivoLlenos = collect();
        foreach ($espaciosPorAlmacen as $nombreAlmacen => $espacios) {
            $ratio = $this->ocupacionObjetivo[$nombreAlmacen] ?? 0.6;
            $cantidadLlenos = max(1, (int) round($espacios->count() * $ratio));
            $espaciosObjetivoLlenos = $espaciosObjetivoLlenos->merge(
                $espacios->take($cantidadLlenos)->pluck('id_espacio')
            );
        }

        $now = Carbon::now();
        $ubicados = 0;
        $detalleIdx = 0;
        $detalleList = $detallesPendientes->values();

        foreach ($espaciosObjetivoLlenos as $espacioId) {
            if ($detalleIdx >= $detalleList->count()) {
                break;
            }

            $detalle = $detalleList[$detalleIdx++];
            $db->table('ubicaciones_donaciones')->insert([
                'id_detalle' => $detalle->id_detalle,
                'id_espacio' => $espacioId,
                'fecha_ingreso' => $now->copy()->subDays(rand(1, 30)),
                'cantidad_ubicada' => (int) $detalle->cantidad,
            ]);
            $db->table('espacios')->where('id_espacio', $espacioId)->update(['estado' => 'lleno']);
            $ubicados++;
        }

        while ($detalleIdx < $detalleList->count()) {
            $detalle = $detalleList[$detalleIdx++];
            $espacioId = $espaciosObjetivoLlenos->random();
            $db->table('ubicaciones_donaciones')->insert([
                'id_detalle' => $detalle->id_detalle,
                'id_espacio' => $espacioId,
                'fecha_ingreso' => $now->copy()->subDays(rand(1, 45)),
                'cantidad_ubicada' => (int) $detalle->cantidad,
            ]);
            $ubicados++;
        }

        $this->command?->info("Inventario: {$ubicados} líneas de stock ubicadas; espacios marcados según ocupación real.");
    }
}
