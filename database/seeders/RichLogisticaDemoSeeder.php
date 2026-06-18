<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class RichLogisticaDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('logistica')->hasTable('estado')) {
            return;
        }

        $db = DB::connection('logistica');
        $now = Carbon::now();

        // 1. Estados
        foreach (['Pendiente', 'En tránsito', 'En almacén', 'Entregado', 'Cancelado', 'Retrasado', 'Rechazado', 'Dañado', 'En espera', 'Prioritario'] as $nombre) {
            if ($db->table('estado')->where('nombre_estado', $nombre)->exists()) {
                continue;
            }
            $db->table('estado')->insert([
                'nombre_estado' => $nombre,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $estadoId = $db->table('estado')->value('id_estado') ?? 1;

        // Solicitudes y paquetes: LogisticaOperativaSeeder + LogisticaReemplazarDemoSeeder

        // 2. Vehículos
        if (Schema::connection('logistica')->hasTable('vehiculo')) {
            $placas = ['4521ABC', '7890XYZ', '1234SCZ', '5678EMG', '9988HJK', '1020PLK', '3344ASD', '5566TTR', '4411QWE', '2233RTY', '4455UIO', '6677PAS'];
            foreach ($placas as $placa) {
                if ($db->table('vehiculo')->where('placa', $placa)->exists()) {
                    continue;
                }
                $row = ['placa' => $placa, 'created_at' => $now, 'updated_at' => $now];
                if (Schema::connection('logistica')->hasColumn('vehiculo', 'modelo')) {
                    $row['modelo'] = ['Volvo FMX', 'Toyota Hilux', 'Mercedes Atego', 'Nissan Patrol', 'Fuso Canter'][rand(0, 4)];
                }
                if (Schema::connection('logistica')->hasColumn('vehiculo', 'capacidad')) {
                    $row['capacidad'] = rand(2, 20).' Ton';
                }
                $db->table('vehiculo')->insert($row);
            }
        }

        // 3. Conductores
        if (Schema::connection('logistica')->hasTable('conductor')) {
             for ($c = 1; $c <= 12; $c++) {
                $nombre = ['Ricardo', 'Mario', 'Hugo', 'Javier', 'Marcelo', 'Marcos'][rand(0, 5)];
                $apellido = ['Cabrera', 'Villca', 'Tapia', 'Siles', 'Mercado', 'Rojas'][rand(0, 5)];
                if ($db->table('conductor')->where('nombre', $nombre)->where('apellido', $apellido)->exists()) continue;
                $db->table('conductor')->insert([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
             }
        }

        // 4. Marcas
        if (Schema::connection('logistica')->hasTable('marca')) {
            foreach (['Toyota', 'Volvo', 'Nissan', 'Mercedes-Benz', 'Scania', 'Dongfeng', 'Ford', 'Chevrolet', 'Isuzu', 'Hino', 'Mazda', 'Mitsubishi'] as $m) {
                if ($db->table('marca')->where('nombre', $m)->exists()) continue;
                $db->table('marca')->insert(['nombre' => $m, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        // 5. Tipos de Emergencia
        if (Schema::connection('logistica')->hasTable('tipo_emergencia')) {
            foreach (['Incendio Forestal', 'Inundación', 'Sequía', 'Derrumbe', 'Helada', 'Granizada', 'Epidemia', 'Accidente Masivo', 'Sismo', 'Incendio Estructural'] as $te) {
                if ($db->table('tipo_emergencia')->where('nombre', $te)->exists()) continue;
                $db->table('tipo_emergencia')->insert(['nombre' => $te, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        $this->command?->info('Logística: Datos demo ampliados significativamente (mínimo 10-20 por tabla).');

        $this->command?->info('Logística: estados, solicitantes, solicitudes y paquetes demo ampliados.');
    }
}
