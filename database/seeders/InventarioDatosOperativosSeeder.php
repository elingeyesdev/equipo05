<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sustituye paquetes, recolecciones, donantes y salidas demo del módulo inventario
 * por registros coherentes con logística operativa (Santa Cruz).
 */
class InventarioDatosOperativosSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('inventario')->hasTable('paquetes')) {
            $this->command?->warn('Inventario: tabla paquetes no disponible.');

            return;
        }

        $this->eliminarPaquetesDemo();
        $this->sincronizarPaquetesDesdeLogistica();
        $this->asignarDetallesPaquetes();
        $this->actualizarRegistrosSalida();
        $this->actualizarDonantesDemo();
        $this->actualizarRecoleccionesDemo();
        $this->actualizarAlmacenesDemo();

        $total = DB::connection('inventario')->table('paquetes')->count();
        $this->command?->info("Inventario operativo: {$total} paquetes alineados con logística y datos reales.");
    }

    private function eliminarPaquetesDemo(): void
    {
        $db = DB::connection('inventario');

        $demoIds = $db->table('paquetes')
            ->where(function ($q) {
                $q->where('codigo_paquete', 'like', '%DEMO%')
                    ->orWhere('codigo_paquete', 'like', 'PKG-RICH%')
                    ->orWhere('codigo_paquete', 'like', 'LOG-DEMO%')
                    ->orWhere(function ($q2) {
                        $q2->whereNull('codigo_solicitud_externa')
                            ->where('codigo_paquete', 'not like', 'PKG-INV-%');
                    });
            })
            ->pluck('id_paquete');

        if ($demoIds->isEmpty()) {
            return;
        }

        if (Schema::connection('inventario')->hasTable('paquete_detalles')) {
            $db->table('paquete_detalles')->whereIn('id_paquete', $demoIds)->delete();
        }

        if (Schema::connection('inventario')->hasTable('registros_salida')) {
            $db->table('registros_salida')->whereIn('id_paquete', $demoIds)->delete();
        }

        $db->table('paquetes')->whereIn('id_paquete', $demoIds)->delete();

        $this->command?->info("Inventario: {$demoIds->count()} paquetes demo eliminados.");
    }

    private function sincronizarPaquetesDesdeLogistica(): void
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            return;
        }

        $inv = DB::connection('inventario');
        $log = DB::connection('logistica');

        $solicitudes = $log->table('solicitud as s')
            ->join('paquete as p', 'p.id_solicitud', '=', 's.id_solicitud')
            ->whereNotIn('s.estado', ['rechazada'])
            ->whereIn('s.estado', ['aprobada', 'en_ruta', 'entregada'])
            ->select(
                's.codigo_seguimiento',
                's.estado',
                'p.fecha_creacion',
                'p.codigo as codigo_logistica'
            )
            ->orderBy('s.id_solicitud')
            ->get();

        $ciAlmacen = $inv->table('usuarios')
            ->where('correo', 'almacen123@gmail.com')
            ->value('ci') ?? '5845210';

        $creados = 0;
        foreach ($solicitudes as $sol) {
            if ($inv->table('paquetes')->where('codigo_solicitud_externa', $sol->codigo_seguimiento)->exists()) {
                continue;
            }

            $estadoInv = match ($sol->estado) {
                'entregada' => 'entregado',
                'en_ruta' => 'en_transito',
                'aprobada' => 'empacado',
                default => 'pendiente',
            };

            $fecha = $sol->fecha_creacion
                ? Carbon::parse($sol->fecha_creacion)
                : Carbon::now();

            $inv->table('paquetes')->insert([
                'codigo_paquete' => 'PKG-INV-'.substr($sol->codigo_seguimiento, 4),
                'codigo_solicitud_externa' => $sol->codigo_seguimiento,
                'estado' => $estadoInv,
                'fecha_creacion' => $fecha,
                'ci_usuario_registro' => $ciAlmacen,
            ]);
            $creados++;
        }

        if ($creados > 0) {
            $this->command?->info("Inventario: {$creados} paquetes vinculados a solicitudes de logística.");
        }
    }

    private function asignarDetallesPaquetes(): void
    {
        if (! Schema::connection('inventario')->hasTable('paquete_detalles')) {
            return;
        }

        $db = DB::connection('inventario');

        $paquetes = $db->table('paquetes')
            ->whereIn('estado', ['empacado', 'en_transito', 'entregado'])
            ->whereNotIn('id_paquete', $db->table('paquete_detalles')->distinct()->pluck('id_paquete'))
            ->pluck('id_paquete');

        if ($paquetes->isEmpty()) {
            return;
        }

        $lotes = $db->table('donacion_detalles as dd')
            ->join('donaciones as d', 'd.id_donacion', '=', 'dd.id_donacion')
            ->where('d.tipo', 'especie')
            ->select('dd.id_detalle', 'dd.cantidad')
            ->orderBy('dd.id_detalle')
            ->get();

        if ($lotes->isEmpty()) {
            return;
        }

        $asignados = 0;
        $loteIdx = 0;

        foreach ($paquetes as $paqueteId) {
            $productosEnPaquete = 0;

            while ($productosEnPaquete < 3 && $loteIdx < $lotes->count()) {
                $lote = $lotes[$loteIdx++];
                $usado = (int) $db->table('paquete_detalles')
                    ->where('id_detalle_donacion', $lote->id_detalle)
                    ->sum('cantidad_usada');
                $disponible = (int) $lote->cantidad - $usado;

                if ($disponible <= 0) {
                    continue;
                }

                $tomar = min($disponible, max(1, (int) round($lote->cantidad * 0.12)));

                $db->table('paquete_detalles')->insert([
                    'id_paquete' => $paqueteId,
                    'id_detalle_donacion' => $lote->id_detalle,
                    'cantidad_usada' => $tomar,
                ]);
                $productosEnPaquete++;
                $asignados++;
            }
        }

        if ($asignados > 0) {
            $this->command?->info("Inventario: {$asignados} líneas de detalle asignadas a paquetes.");
        }
    }

    private function actualizarRegistrosSalida(): void
    {
        if (! Schema::connection('inventario')->hasTable('registros_salida')) {
            return;
        }

        $inv = DB::connection('inventario');
        $log = Schema::connection('logistica')->hasTable('solicitud')
            ? DB::connection('logistica')
            : null;

        $inv->table('registros_salida')
            ->where(function ($q) {
                $q->where('observaciones', 'like', '%demo%')
                    ->orWhere('destino', 'like', '%demo%');
            })
            ->delete();

        if (! $log) {
            return;
        }

        $paquetesSalida = $inv->table('paquetes')
            ->whereIn('estado', ['en_transito', 'entregado'])
            ->whereNotNull('codigo_solicitud_externa')
            ->whereNotIn('id_paquete', $inv->table('registros_salida')->pluck('id_paquete'))
            ->get();

        $encargado = $inv->table('usuarios')
            ->where('correo', 'almacen123@gmail.com')
            ->select('nombres', 'apellidos')
            ->first();

        $nombreEncargado = $encargado
            ? trim($encargado->nombres.' '.$encargado->apellidos)
            : 'Pedro Almacen';

        foreach ($paquetesSalida as $paquete) {
            $destino = $log->table('solicitud as s')
                ->join('destino as d', 'd.id_destino', '=', 's.id_destino')
                ->where('s.codigo_seguimiento', $paquete->codigo_solicitud_externa)
                ->select('d.comunidad', 'd.provincia', 'd.direccion', 's.tipo_emergencia')
                ->first();

            if (! $destino) {
                continue;
            }

            $inv->table('registros_salida')->insert([
                'id_paquete' => $paquete->id_paquete,
                'fecha_salida' => Carbon::parse($paquete->fecha_creacion)->addHours(6),
                'destino' => trim($destino->comunidad.', '.$destino->provincia),
                'observaciones' => 'Despacho por '.$destino->tipo_emergencia.' — '.$destino->direccion,
                'encargado' => $nombreEncargado,
            ]);
        }
    }

    private function actualizarDonantesDemo(): void
    {
        $db = DB::connection('inventario');

        $donantes = $db->table('donantes')
            ->where('email', 'like', '%@demo.local')
            ->orderBy('id_donante')
            ->get();

        foreach ($donantes as $i => $donante) {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $donante->nombre));
            $slug = trim($slug, '.');
            $email = $slug.'@donaciones.scz.bo';

            if ($db->table('donantes')->where('email', $email)->where('id_donante', '!=', $donante->id_donante)->exists()) {
                $email = $slug.'.'.($i + 1).'@donaciones.scz.bo';
            }

            $db->table('donantes')->where('id_donante', $donante->id_donante)->update([
                'email' => $email,
            ]);
        }

        $db->table('donaciones')
            ->where('observaciones', 'like', '%demo%')
            ->update([
                'observaciones' => DB::raw("REPLACE(observaciones, 'demo', 'solidaria')"),
            ]);

        $db->table('productos')
            ->where('descripcion', 'like', 'Producto demo%')
            ->update([
                'descripcion' => DB::raw("REPLACE(descripcion, 'Producto demo', 'Insumo de emergencia')"),
            ]);
    }

    private function actualizarRecoleccionesDemo(): void
    {
        if (! Schema::connection('inventario')->hasTable('solicitudes_recoleccion')) {
            return;
        }

        $db = DB::connection('inventario');

        $direcciones = [
            'Av. Cristo Redentor km 8, Plan 3000, Santa Cruz',
            'Av. San Martín esq. 2do anillo, Equipetrol, Santa Cruz',
            'Mercado Abasto, zona sur, Santa Cruz',
            'Plaza principal Warnes, Santa Cruz',
            'Doble vía La Guardia km 12, Villa 1ro de Mayo',
            'Av. Banzer km 7, Depósito Norte, Santa Cruz',
            'Zona industrial Cotoca, Santa Cruz',
            'Barrio Hamacas, Av. Roca y Coronado, Santa Cruz',
        ];

        $recolecciones = $db->table('solicitudes_recoleccion')
            ->where(function ($q) {
                $q->where('direccion_recoleccion', 'like', '%Demo%')
                    ->orWhere('observaciones', 'like', '%demo%')
                    ->orWhere('observaciones', 'like', '%Recolección programada SOL-REC%');
            })
            ->orderBy('id_solicitud')
            ->get();

        foreach ($recolecciones as $i => $rec) {
            $dir = $direcciones[$i % count($direcciones)];
            $codigo = 'REC-SCZ-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);

            $db->table('solicitudes_recoleccion')
                ->where('id_solicitud', $rec->id_solicitud)
                ->update([
                    'direccion_recoleccion' => $dir,
                    'observaciones' => 'Recolección programada '.$codigo.' — donación en especie',
                ]);
        }

        if ($recolecciones->isNotEmpty()) {
            $this->command?->info("Inventario: {$recolecciones->count()} solicitudes de recolección actualizadas.");
        }
    }

    private function actualizarAlmacenesDemo(): void
    {
        if (! Schema::connection('inventario')->hasTable('almacenes')) {
            return;
        }

        $db = DB::connection('inventario');
        $mapa = [
            'Central SCZ' => 'Av. San Martín esq. 2do anillo, Equipetrol, Santa Cruz',
            'Depósito Norte' => 'Av. Banzer km 7, zona norte, Santa Cruz',
            'Bodega Cotoca' => 'Zona industrial Cotoca, Santa Cruz',
            'Punto Plan 3000' => 'Av. Cumavi km 8, Plan 3000, Santa Cruz',
        ];

        foreach ($mapa as $nombre => $direccion) {
            $db->table('almacenes')
                ->where('nombre', $nombre)
                ->where(function ($q) {
                    $q->where('direccion', 'like', '%demo%')
                        ->orWhere('direccion', 'like', 'Dirección demo%');
                })
                ->update(['direccion' => $direccion]);
        }
    }
}
