<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichSeguimientoDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('seguimiento')->hasTable('usuario')) {
            return;
        }

        $db = DB::connection('seguimiento');
        $now = Carbon::now();

        $voluntarios = [
            ['Juan', 'Pérez', 'juan.voluntario@demo.local'],
            ['María', 'González', 'maria.voluntario@demo.local'],
            ['Pedro', 'Ramos', 'pedro.voluntario@demo.local'],
            ['Lucía', 'Vargas', 'lucia.voluntario@demo.local'],
            ['Roberto', 'Díaz', 'roberto.voluntario@demo.local'],
            ['Elena', 'Suárez', 'elena.voluntario@demo.local'],
        ];

        foreach ($voluntarios as [$nombre, $apellido, $email]) {
            if ($db->table('usuario')->where('email', $email)->exists()) {
                continue;
            }
            $db->table('usuario')->insert([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'activo' => true,
                'administrador' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $capacitaciones = [
            'Primeros auxilios', 'Manejo de incendios', 'Logística humanitaria',
            'Comunicación en emergencias', 'Trabajo en equipo', 'Uso de GPS campo',
        ];

        foreach ($capacitaciones as $nombre) {
            if ($db->table('capacitacion')->where('nombre', $nombre)->exists()) {
                continue;
            }
            $db->table('capacitacion')->insert([
                'nombre' => $nombre,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::connection('seguimiento')->hasTable('necesidad')) {
            foreach (['Agua', 'Alimentos', 'Transporte', 'Medicamentos', 'Refugio'] as $nombre) {
                if ($db->table('necesidad')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('necesidad')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::connection('seguimiento')->hasTable('solicitudes_ayuda')) {
            for ($i = 1; $i <= 10; $i++) {
                if ($db->table('solicitudes_ayuda')->count() >= 15) {
                    break;
                }
                $db->table('solicitudes_ayuda')->insert([
                    'estado' => ['pendiente', 'en_proceso', 'atendida'][rand(0, 2)],
                    'created_at' => $now->copy()->subDays(rand(0, 20)),
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::connection('seguimiento')->hasTable('chat_mensajes')) {
            $mensajes = [
                'Brigada lista para salir a zona norte.',
                'Necesitamos más frazadas en el punto de acopio.',
                'Confirmado traslado de 3 toneladas de agua.',
                'Voluntarios reunidos en base Plan 3000.',
            ];
            foreach ($mensajes as $mensaje) {
                if ($db->table('chat_mensajes')->where('mensaje', $mensaje)->exists()) {
                    continue;
                }
                $db->table('chat_mensajes')->insert([
                    'mensaje' => $mensaje,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Seguimiento: voluntarios, capacitaciones y actividad demo ampliados.');
    }
}
