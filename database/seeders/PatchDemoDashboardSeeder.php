<?php

namespace Database\Seeders;

use App\Support\UnifiedPostgres;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PatchDemoDashboardSeeder extends Seeder
{
    public function run(): void
    {
        if (! UnifiedPostgres::enabled()) {
            return;
        }

        $this->fixRolesGuard();
        $this->fixTransparenciaDonaciones();
        $this->fixTransparenciaMensajesYDetalles();
        $this->fixInventarioHuecos();
        $this->fixInventarioStockUbicado();
        $this->fixInventarioUsuarios();
        $this->fixInventarioDatosOperativos();
        $this->fixLogisticaDemo();
        $this->fixRescateDashboard();

        $this->command?->info('Parche demo: roles, dashboard transparencia, inventario y rescate completados.');
    }

    private function fixRolesGuard(): void
    {
        $db = DB::connection('core');
        $broken = $db->table('roles')
            ->where(function ($q) {
                $q->whereNull('guard_name')->orWhere('guard_name', '');
            })
            ->get();

        foreach ($broken as $role) {
            $canonical = $db->table('roles')
                ->where('name', $role->name)
                ->where('guard_name', 'web')
                ->first();

            if ($canonical) {
                $db->table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->update(['role_id' => $canonical->id]);
                $db->table('role_has_permissions')
                    ->where('role_id', $role->id)
                    ->delete();
                $db->table('roles')->where('id', $role->id)->delete();
            } else {
                $db->table('roles')->where('id', $role->id)->update([
                    'guard_name' => 'web',
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function fixTransparenciaDonaciones(): void
    {
        if (! Schema::connection('transparencia')->hasTable('donaciones')) {
            return;
        }

        $db = DB::connection('transparencia');

        if ($db->table('estados')->count() < 4) {
            foreach (['Pendiente', 'Confirmada', 'Asignada', 'Entregada'] as $nombre) {
                if (! $db->table('estados')->where('nombre', $nombre)->exists()) {
                    $db->table('estados')->insert(['nombre' => $nombre, 'descripcion' => 'Estado demo']);
                }
            }
        }

        $estados = $db->table('estados')->pluck('estadoid', 'nombre');
        $confirmada = $estados['Confirmada'] ?? $estados->first();
        $asignada = $estados['Asignada'] ?? $confirmada;
        $entregada = $estados['Entregada'] ?? $confirmada;
        $pendiente = $estados['Pendiente'] ?? $confirmada;

        $db->table('donaciones')
            ->whereIn('tipodonacion', ['dinero', 'Dinero', 'monetaria', 'Monetario'])
            ->update(['tipodonacion' => 'Monetaria']);

        $monetarias = $db->table('donaciones')
            ->where('tipodonacion', 'Monetaria')
            ->orderBy('donacionid')
            ->get(['donacionid']);

        foreach ($monetarias as $i => $row) {
            $estado = match ($i % 5) {
                0, 1, 2 => $confirmada,
                3 => $asignada,
                default => $entregada,
            };
            if ($i % 7 === 0) {
                $estado = $pendiente;
            }
            $db->table('donaciones')->where('donacionid', $row->donacionid)->update(['estadoid' => $estado]);
        }

        if (Schema::connection('transparencia')->hasTable('campanias')) {
            $campanias = $db->table('campanias')->get(['campaniaid']);
            foreach ($campanias as $camp) {
                $suma = (float) $db->table('donaciones')
                    ->where('campaniaid', $camp->campaniaid)
                    ->where('tipodonacion', 'Monetaria')
                    ->whereIn('estadoid', [$confirmada, $asignada, $entregada])
                    ->sum('monto');

                $db->table('campanias')->where('campaniaid', $camp->campaniaid)->update([
                    'montorecaudado' => $suma,
                ]);
            }
        }
    }

    private function fixTransparenciaMensajesYDetalles(): void
    {
        if (! Schema::connection('transparencia')->hasTable('mensajes')) {
            return;
        }

        $db = DB::connection('transparencia');
        $userId = DB::connection('core')->table('usuarios')->value('usuarioid') ?? 1;
        $now = Carbon::now();

        if ($db->table('mensajes')->count() < 5) {
            $convId = $db->table('conversaciones')->value('conversacionid');
            if (! $convId) {
                $convId = $db->table('conversaciones')->insertGetId([
                    'tipo' => 'private',
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'conversacionid');
                $db->table('conversacion_usuarios')->insert([
                    'conversacionid' => $convId,
                    'usuarioid' => $userId,
                    'ultimo_leido' => $now,
                ]);
            }

            $asuntos = [
                'Consulta sobre campaña Chiquitania',
                'Comprobante de transferencia adjunto',
                'Solicitud de actualización de asignación',
                'Voluntariado para recolección',
                'Reporte de entrega en zona norte',
                'Donación anónima confirmada',
            ];

            foreach ($asuntos as $i => $asunto) {
                if ($db->table('mensajes')->where('asunto', $asunto)->exists()) {
                    continue;
                }
                $db->table('mensajes')->insert([
                    'conversacionid' => $convId,
                    'usuarioid' => $userId,
                    'asunto' => $asunto,
                    'contenido' => 'Mensaje demo #'.($i + 1).': seguimiento de donaciones y logística solidaria.',
                    'fechaenvio' => $now->copy()->subHours($i * 3),
                    'leido' => $i % 2 === 0,
                ]);
            }
        }

        if (Schema::connection('transparencia')->hasTable('detallesasignacion')) {
            $asignaciones = $db->table('asignaciones')->get(['asignacionid', 'monto']);
            foreach ($asignaciones as $asig) {
                if ($db->table('detallesasignacion')->where('asignacionid', $asig->asignacionid)->exists()) {
                    continue;
                }
                $conceptos = ['Alimentos', 'Frazadas', 'Medicamentos', 'Transporte'];
                $restante = (float) $asig->monto;
                foreach ($conceptos as $j => $concepto) {
                    $precio = round($restante / (count($conceptos) - $j), 2);
                    $restante -= $precio;
                    $db->table('detallesasignacion')->insert([
                        'asignacionid' => $asig->asignacionid,
                        'concepto' => $concepto,
                        'cantidad' => rand(5, 40),
                        'preciounitario' => max(10, $precio / rand(5, 15)),
                        'imagenurl' => 'https://picsum.photos/seed/det'.$asig->asignacionid.$j.'/400/300',
                    ]);
                }
            }
        }
    }

    private function fixRescateDashboard(): void
    {
        if (! Schema::connection('rescate')->hasTable('animal_files')) {
            return;
        }

        $seeder = new RichRescateDemoSeeder;
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->enrichDashboardDemoData();
        $seeder->runSpeciesImageRefresh();
    }

    private function fixInventarioDatosOperativos(): void
    {
        if (! Schema::connection('inventario')->hasTable('paquetes')) {
            return;
        }

        $inv = DB::connection('inventario');
        $tieneDemo = $inv->table('paquetes')
            ->where(function ($q) {
                $q->where('codigo_paquete', 'like', '%DEMO%')
                    ->orWhere('codigo_paquete', 'like', 'PKG-RICH%')
                    ->orWhere('codigo_paquete', 'like', 'LOG-DEMO%');
            })
            ->exists();

        $donantesDemo = Schema::connection('inventario')->hasTable('donantes')
            && $inv->table('donantes')->where('email', 'like', '%@demo.local')->exists();

        $recoleccionesDemo = Schema::connection('inventario')->hasTable('solicitudes_recoleccion')
            && $inv->table('solicitudes_recoleccion')->where('direccion_recoleccion', 'like', '%Demo%')->exists();

        if (! $tieneDemo && ! $donantesDemo && ! $recoleccionesDemo) {
            return;
        }

        $seeder = new InventarioDatosOperativosSeeder;
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->run();
    }

    private function fixLogisticaDemo(): void
    {
        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            return;
        }

        $tieneDemo = DB::connection('logistica')->table('solicitud')
            ->where('codigo_seguimiento', 'like', 'LOG-DEMO-%')
            ->exists();

        if (! $tieneDemo) {
            return;
        }

        $seeder = new LogisticaReemplazarDemoSeeder;
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->run();
    }

    private function fixInventarioUsuarios(): void
    {
        if (! Schema::connection('inventario')->hasTable('usuarios')) {
            return;
        }

        if (DB::connection('inventario')->table('usuarios')->count() >= 5) {
            return;
        }

        $seeder = new InventarioUsuariosOperativosSeeder;
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->run();
    }

    private function fixInventarioStockUbicado(): void
    {
        if (! Schema::connection('inventario')->hasTable('ubicaciones_donaciones')) {
            return;
        }

        $inv = DB::connection('inventario');
        $sinUbicar = $inv->table('donacion_detalles')
            ->whereNotIn('id_detalle', $inv->table('ubicaciones_donaciones')->pluck('id_detalle'))
            ->exists();

        $sinOcupacion = $inv->table('espacios')
            ->whereRaw("LOWER(COALESCE(estado, 'disponible')) = 'lleno'")
            ->doesntExist();

        if (! $sinUbicar && ! $sinOcupacion) {
            return;
        }

        $seeder = new InventarioUbicarStockSeeder;
        if ($this->command) {
            $seeder->setCommand($this->command);
        }
        $seeder->run();
    }

    private function fixInventarioHuecos(): void
    {
        if (! Schema::connection('inventario')->hasTable('puntos_recoleccion')) {
            return;
        }

        $db = DB::connection('inventario');
        $now = Carbon::now();

        $puntos = [
            ['Punto Plan 3000', 'Av. Cumavi km 8', -17.8132, -63.2011],
            ['Punto Equipetrol', 'Av. San Martín', -17.7634, -63.1822],
            ['Punto Mercado Abasto', 'Zona Abasto', -17.7891, -63.1544],
            ['Punto Warnes', 'Plaza principal Warnes', -17.5123, -63.1678],
            ['Punto Villa 1ro de Mayo', 'Doble vía La Guardia', -17.8712, -63.2456],
        ];

        foreach ($puntos as [$nombre, $dir, $lat, $lng]) {
            if ($db->table('puntos_recoleccion')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('puntos_recoleccion')->insert([
                'nombre' => $nombre,
                'direccion' => $dir,
                'contacto' => '700'.rand(10000, 99999),
                'latitud' => $lat,
                'longitud' => $lng,
            ]);
        }

        if (Schema::connection('inventario')->hasColumn('almacenes', 'latitud')) {
            $coords = [
                'Central SCZ' => [-17.7833, -63.1821],
                'Depósito Norte' => [-17.7452, -63.1601],
                'Bodega Cotoca' => [-17.7540, -62.9800],
                'Punto Plan 3000' => [-17.8527, -63.2215],
            ];
            foreach ($coords as $nombre => [$lat, $lng]) {
                $db->table('almacenes')->where('nombre', $nombre)->update([
                    'latitud' => $lat,
                    'longitud' => $lng,
                ]);
            }
        }

        if (Schema::connection('inventario')->hasTable('registros_salida')
            && $db->table('registros_salida')->count() === 0
            && $db->table('paquetes')->whereNotNull('codigo_solicitud_externa')->exists()) {
            $seeder = new InventarioDatosOperativosSeeder;
            if ($this->command) {
                $seeder->setCommand($this->command);
            }
            $seeder->run();
        }
    }
}
