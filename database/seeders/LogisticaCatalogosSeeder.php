<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaCatalogosSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('ubicacion')) {
            $this->command?->warn('Logística: esquema de catálogos no disponible.');

            return;
        }

        $db = DB::connection('logistica');
        $now = Carbon::now();

        $this->sembrarUbicaciones($db, $now);
        $this->sembrarMarcas($db, $now);
        $this->sembrarTiposVehiculo($db, $now);
        $this->sembrarTiposLicencia($db, $now);
        $this->sembrarTiposEmergencia($db, $now);
        $this->sembrarRoles($db, $now);
        $this->sembrarUsuariosOperativos($db, $now);
        $this->sembrarReportes($db, $now);
        $this->enriquecerConductores($db, $now);

        $this->command?->info('Logística: catálogos operativos (ubicaciones, marcas, tipos, reportes) sembrados.');
    }

    private function sembrarUbicaciones($db, Carbon $now): void
    {
        $puntos = [
            'Almacén central — Plan 3000, Av. Cristo Redentor (Santa Cruz)',
            'Centro de acopio — Warnes, zona norte',
            'Punto de despacho — Montero, mercado municipal',
            'Base logística — El Torno, carretera a La Guardia',
            'Centro de acopio — San Ignacio de Velasco',
            'Depósito regional — Concepción, Ñuflo de Chávez',
            'Punto intermedio — Pailón, Sara',
            'Acopio Chiquitanía — Roboré',
            'Centro de distribución — Puerto Suárez',
            'Punto de entrega — Yapacaní, Ichilo',
            'Base de operaciones — Charagua, Chaco',
            'Acopio Cordillera — La Guardia',
            'Depósito temporal — Cotoca',
            'Punto móvil — Ruta SCZ–Montero km 18',
            'Terminal de carga — Doble vía La Guardia',
            'Centro de acopio — San Matías, Germán Busch',
            'Punto fronterizo — Bolpebra (apoyo logístico)',
            'Almacén satélite — Mineros, Chapare',
            'Centro de acopio — Buena Vista, Ichilo',
            'Punto de relevo — Samaipata, Florida',
        ];

        foreach ($puntos as $descripcion) {
            if ($db->table('ubicacion')->where('descripcion', $descripcion)->exists()) {
                continue;
            }
            $db->table('ubicacion')->insert([
                'descripcion' => $descripcion,
                'created_at' => $now->copy()->subDays(rand(5, 90)),
                'updated_at' => $now,
            ]);
        }
    }

    private function sembrarMarcas($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('marca')) {
            return;
        }

        foreach (['Toyota', 'Volvo', 'Mercedes-Benz', 'Scania', 'Nissan', 'Isuzu', 'Ford', 'Chevrolet', 'Hino', 'Mitsubishi', 'Dongfeng', 'JAC'] as $nombre) {
            if ($db->table('marca')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('marca')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarTiposVehiculo($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('tipo_vehiculo')) {
            return;
        }

        foreach ([
            'Camión de carga pesada',
            'Camión rígido',
            'Pickup doble cabina',
            'Furgón cerrado',
            'Camioneta 4x4',
            'Trailer semirremolque',
            'Motocarga',
            'Ambulancia de apoyo',
        ] as $nombre) {
            if ($db->table('tipo_vehiculo')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('tipo_vehiculo')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarTiposLicencia($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('tipo_licencia')) {
            return;
        }

        foreach (['Licencia A — motocicletas', 'Licencia B — vehículos livianos', 'Licencia C — camiones medianos', 'Licencia D — transporte pesado', 'Licencia E — tracción articulada'] as $nombre) {
            if ($db->table('tipo_licencia')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('tipo_licencia')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarTiposEmergencia($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('tipo_emergencia')) {
            return;
        }

        foreach ([
            'Incendio forestal', 'Incendio estructural', 'Inundación', 'Sequía severa',
            'Derrumbe', 'Granizada', 'Helada', 'Accidente vial masivo', 'Epidemia', 'Sismo',
        ] as $nombre) {
            if ($db->table('tipo_emergencia')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('tipo_emergencia')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarRoles($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('rol')) {
            return;
        }

        foreach (['Conductor', 'Despachador', 'Encargado de almacén', 'Coordinador de ruta', 'Auxiliar logístico'] as $nombre) {
            if ($db->table('rol')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('rol')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarUsuariosOperativos($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('usuario')) {
            return;
        }

        $personal = [
            'Ana Despachadora SCZ',
            'Carlos Supervisor de rutas',
            'María Encargada Warnes',
            'Pedro Coordinador Chiquitos',
            'Lucía Operadora Plan 3000',
            'Jorge Auxiliar nocturno',
            'Sofía Enlace con inventario',
            'Diego Monitoreo GPS',
        ];

        foreach ($personal as $nombre) {
            if ($db->table('usuario')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('usuario')->insert(['nombre' => $nombre, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function sembrarReportes($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('reporte')) {
            return;
        }

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'];
        foreach ($meses as $i => $mes) {
            $titulo = "Informe operativo logístico — {$mes} 2026";
            if ($db->table('reporte')->where('titulo', $titulo)->exists()) {
                continue;
            }
            $db->table('reporte')->insert([
                'titulo' => $titulo,
                'created_at' => $now->copy()->subMonths(5 - $i)->startOfMonth(),
                'updated_at' => $now,
            ]);
        }

        foreach ([
            'Resumen entregas zona norte (Warnes–Montero)',
            'Balance de flota activa — Q2 2026',
            'Solicitudes atendidas vs. pendientes',
            'Tiempos promedio de entrega por provincia',
        ] as $titulo) {
            if ($db->table('reporte')->where('titulo', $titulo)->exists()) {
                continue;
            }
            $db->table('reporte')->insert(['titulo' => $titulo, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function enriquecerConductores($db, Carbon $now): void
    {
        if (! Schema::connection('logistica')->hasTable('conductor')) {
            return;
        }

        $extras = [
            ['Felipe', 'Condori'],
            ['Oscar', 'Aguilera'],
            ['Daniel', 'Justiniano'],
            ['René', 'Velasco'],
            ['Tomás', 'Barrientos'],
        ];

        foreach ($extras as [$nombre, $apellido]) {
            if ($db->table('conductor')->where('nombre', $nombre)->where('apellido', $apellido)->exists()) {
                continue;
            }
            $db->table('conductor')->insert([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
