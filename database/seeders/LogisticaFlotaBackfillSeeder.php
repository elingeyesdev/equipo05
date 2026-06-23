<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LogisticaFlotaBackfillSeeder extends Seeder
{
    /** @var array<int, array<string, string>> */
    private array $conductores = [
        ['nombre' => 'Ricardo', 'apellido' => 'Cabrera', 'ci' => '5843210', 'telefono' => '72110001', 'email' => 'ricardo.cabrera@correo.com'],
        ['nombre' => 'Mario', 'apellido' => 'Villca', 'ci' => '7123456', 'telefono' => '72110002', 'email' => 'mario.villca@correo.com'],
        ['nombre' => 'Hugo', 'apellido' => 'Tapia', 'ci' => '6234789', 'telefono' => '72110003', 'email' => 'hugo.tapia@correo.com'],
        ['nombre' => 'Javier', 'apellido' => 'Siles', 'ci' => '8012345', 'telefono' => '72110004', 'email' => 'javier.siles@correo.com'],
        ['nombre' => 'Marcelo', 'apellido' => 'Mercado', 'ci' => '4567890', 'telefono' => '72110005', 'email' => 'marcelo.mercado@correo.com'],
        ['nombre' => 'Felipe', 'apellido' => 'Rojas', 'ci' => '6987412', 'telefono' => '72110006', 'email' => 'felipe.rojas@correo.com'],
        ['nombre' => 'Oscar', 'apellido' => 'Condori', 'ci' => '7456123', 'telefono' => '72110007', 'email' => 'oscar.condori@correo.com'],
        ['nombre' => 'Daniel', 'apellido' => 'Aguilera', 'ci' => '5321987', 'telefono' => '72110008', 'email' => 'daniel.aguilera@correo.com'],
        ['nombre' => 'Felipe', 'apellido' => 'Condori', 'ci' => '6011223', 'telefono' => '72110009', 'email' => 'felipe.condori@correo.com'],
        ['nombre' => 'Oscar', 'apellido' => 'Aguilera', 'ci' => '6011224', 'telefono' => '72110010', 'email' => 'oscar.aguilera@correo.com'],
        ['nombre' => 'Daniel', 'apellido' => 'Justiniano', 'ci' => '6011225', 'telefono' => '72110011', 'email' => 'daniel.justiniano@correo.com'],
        ['nombre' => 'René', 'apellido' => 'Velasco', 'ci' => '6011226', 'telefono' => '72110012', 'email' => 'rene.velasco@correo.com'],
        ['nombre' => 'Tomás', 'apellido' => 'Barrientos', 'ci' => '6011227', 'telefono' => '72110013', 'email' => 'tomas.barrientos@correo.com'],
    ];

    /** @var array<int, array<string, mixed>> */
    private array $vehiculosPlantilla = [
        ['modelo' => 'Toyota Hilux 4x4', 'marca' => 'Toyota', 'tipo' => 'Camioneta 4x4', 'capacidad' => '3 Ton', 'anio' => 2020],
        ['modelo' => 'Volvo FMX', 'marca' => 'Volvo', 'tipo' => 'Camión de carga pesada', 'capacidad' => '12 Ton', 'anio' => 2019],
        ['modelo' => 'Mercedes Atego', 'marca' => 'Mercedes-Benz', 'tipo' => 'Camión rígido', 'capacidad' => '8 Ton', 'anio' => 2018],
        ['modelo' => 'Nissan Patrol', 'marca' => 'Nissan', 'tipo' => 'Pickup doble cabina', 'capacidad' => '2 Ton', 'anio' => 2021],
        ['modelo' => 'Isuzu NPR', 'marca' => 'Isuzu', 'tipo' => 'Furgón cerrado', 'capacidad' => '5 Ton', 'anio' => 2017],
        ['modelo' => 'Scania P360', 'marca' => 'Scania', 'tipo' => 'Trailer semirremolque', 'capacidad' => '20 Ton', 'anio' => 2022],
    ];

    public function run(): void
    {
        $schema = Schema::connection('logistica');
        if (! $schema->hasTable('conductor') && ! $schema->hasTable('vehiculo')) {
            $this->command?->warn('Logística: tablas de flota no disponibles.');

            return;
        }

        $db = DB::connection('logistica');
        $licenciaId = $this->resolverLicenciaConductor($db, $schema);
        $actualizadosConductores = $this->backfillConductores($db, $schema, $licenciaId);
        $actualizadosVehiculos = $this->backfillVehiculos($db, $schema);

        $this->command?->info("Logística flota: {$actualizadosConductores} conductores y {$actualizadosVehiculos} vehículos actualizados.");
    }

    private function resolverLicenciaConductor($db, $schema): ?int
    {
        if (! $schema->hasTable('tipo_licencia')) {
            return null;
        }

        $nombreCol = $schema->hasColumn('tipo_licencia', 'tipo_licencia') ? 'tipo_licencia' : 'nombre';
        $pk = $schema->hasColumn('tipo_licencia', 'id_tipo_licencia') ? 'id_tipo_licencia' : 'id_licencia';

        $id = $db->table('tipo_licencia')
            ->where($nombreCol, 'like', '%Licencia C%')
            ->value($pk);

        if ($id === null) {
            $id = $db->table('tipo_licencia')->orderBy($pk)->value($pk);
        }

        return $id !== null ? (int) $id : null;
    }

    private function backfillConductores($db, $schema, ?int $licenciaId): int
    {
        if (! $schema->hasTable('conductor')) {
            return 0;
        }

        $count = 0;
        $conductoresDb = $db->table('conductor')->orderBy('id_conductor')->get();

        foreach ($conductoresDb as $index => $row) {
            $plantilla = $this->conductores[$index % count($this->conductores)];

            if ($row->nombre === $plantilla['nombre'] && $row->apellido === $plantilla['apellido']) {
                $fuente = $plantilla;
            } else {
                $fuente = [
                    'ci' => str_pad((string) (6000000 + (int) $row->id_conductor), 7, '0', STR_PAD_LEFT),
                    'telefono' => '72'.str_pad((string) (10000 + (int) $row->id_conductor), 5, '0', STR_PAD_LEFT),
                    'email' => strtolower(preg_replace('/\s+/', '.', trim(($row->nombre ?? 'conductor').'.'.($row->apellido ?? 'flota')))).'@correo.com',
                ];
            }

            $update = [];
            if ($schema->hasColumn('conductor', 'ci') && empty($row->ci)) {
                $update['ci'] = $fuente['ci'];
            }
            if ($schema->hasColumn('conductor', 'telefono') && empty($row->telefono)) {
                $update['telefono'] = $fuente['telefono'];
            }
            if ($schema->hasColumn('conductor', 'email') && empty($row->email)) {
                $update['email'] = $fuente['email'];
            }
            if ($schema->hasColumn('conductor', 'id_licencia') && empty($row->id_licencia) && $licenciaId !== null) {
                $update['id_licencia'] = $licenciaId;
            }

            if ($update !== []) {
                $update['updated_at'] = now();
                $db->table('conductor')->where('id_conductor', $row->id_conductor)->update($update);
                $count++;
            }
        }

        return $count;
    }

    private function backfillVehiculos($db, $schema): int
    {
        if (! $schema->hasTable('vehiculo')) {
            return 0;
        }

        $marcas = $schema->hasTable('marca')
            ? $db->table('marca')->pluck('id_marca', $schema->hasColumn('marca', 'nombre_marca') ? 'nombre_marca' : 'nombre')->all()
            : [];
        $tipoPk = $schema->hasTable('tipo_vehiculo')
            ? ($schema->hasColumn('tipo_vehiculo', 'id_tipovehiculo') ? 'id_tipovehiculo' : 'id_tipo_vehiculo')
            : null;
        $tipoNombreCol = null;
        if ($schema->hasTable('tipo_vehiculo')) {
            foreach (['nombre_tipovehiculo', 'nombre_tipo_vehiculo', 'nombre'] as $candidate) {
                if ($schema->hasColumn('tipo_vehiculo', $candidate)) {
                    $tipoNombreCol = $candidate;
                    break;
                }
            }
        }
        $tipos = ($tipoPk && $tipoNombreCol)
            ? $db->table('tipo_vehiculo')->pluck($tipoPk, $tipoNombreCol)->all()
            : [];

        $tipoFk = $schema->hasColumn('vehiculo', 'id_tipovehiculo')
            ? 'id_tipovehiculo'
            : ($schema->hasColumn('vehiculo', 'id_tipo_vehiculo') ? 'id_tipo_vehiculo' : null);

        $count = 0;
        $vehiculos = $db->table('vehiculo')->orderBy('id_vehiculo')->get();

        foreach ($vehiculos as $index => $row) {
            $plantilla = $this->vehiculosPlantilla[$index % count($this->vehiculosPlantilla)];
            $update = [];

            if ($schema->hasColumn('vehiculo', 'modelo') && empty($row->modelo)) {
                $update['modelo'] = $plantilla['modelo'];
            }
            if ($schema->hasColumn('vehiculo', 'anio') && empty($row->anio)) {
                $update['anio'] = $plantilla['anio'];
            }
            if ($schema->hasColumn('vehiculo', 'capacidad') && empty($row->capacidad)) {
                $update['capacidad'] = $plantilla['capacidad'];
            }
            if ($schema->hasColumn('vehiculo', 'id_marca') && empty($row->id_marca) && $marcas !== []) {
                $marcaId = $marcas[$plantilla['marca']] ?? reset($marcas);
                if ($marcaId) {
                    $update['id_marca'] = $marcaId;
                }
            }
            if ($tipoFk && empty($row->{$tipoFk}) && $tipos !== []) {
                $tipoId = $tipos[$plantilla['tipo']] ?? reset($tipos);
                if ($tipoId) {
                    $update[$tipoFk] = $tipoId;
                }
            }
            if ($schema->hasColumn('vehiculo', 'observaciones') && empty($row->observaciones)) {
                $update['observaciones'] = 'Unidad operativa — revisión al día';
            }

            if ($update !== []) {
                $update['updated_at'] = now();
                $db->table('vehiculo')->where('id_vehiculo', $row->id_vehiculo)->update($update);
                $count++;
            }
        }

        return $count;
    }
}
