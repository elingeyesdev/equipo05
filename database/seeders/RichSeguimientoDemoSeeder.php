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

        // 1. Usuarios / Voluntarios
        $nombres = ['Juan', 'María', 'Pedro', 'Lucía', 'Roberto', 'Elena', 'Carlos', 'Sofía', 'Luis', 'Ana', 'Diego', 'Carmen', 'Jorge', 'Raquel', 'Fernando'];
        $apellidos = ['Pérez', 'González', 'Ramos', 'Vargas', 'Díaz', 'Suárez', 'Flores', 'Mendoza', 'Quispe', 'Mamani', 'Rojas', 'Blanco', 'Torres', 'Sosa', 'Luna'];

        for ($i = 0; $i < 20; $i++) {
            $nombre = $nombres[rand(0, count($nombres) - 1)];
            $apellido = $apellidos[rand(0, count($apellidos) - 1)];
            $email = strtolower($nombre . '.' . $apellido . rand(1, 99) . '@voluntario.bo');

            if ($db->table('usuario')->where('email', $email)->exists()) continue;

            $db->table('usuario')->insert([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'activo' => (bool) rand(0, 1),
                'administrador' => (i === 0),
                'created_at' => $now->subDays(rand(1, 60)),
                'updated_at' => $now,
            ]);
        }

        // 2. Capacitaciones
        $capacitaciones = [
            'Primeros auxilios básicos', 'Manejo de incendios forestales', 'Logística humanitaria',
            'Comunicación en emergencias', 'Trabajo en equipo y liderazgo', 'Uso de GPS y cartografía',
            'Rescate en estructuras colapsadas', 'Gestión de refugios temporales', 'Psicología en desastres',
            'Evaluación de daños y necesidades (EDAN)', 'Soporte vital avanzado', 'Manejo de materiales peligrosos'
        ];

        foreach ($capacitaciones as $nombre) {
            if ($db->table('capacitacion')->where('nombre', $nombre)->exists()) continue;
            $db->table('capacitacion')->insert([
                'nombre' => $nombre,
                'descripcion' => 'Capacitación integral sobre ' . $nombre,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Necesidades
        if (Schema::connection('seguimiento')->hasTable('necesidad')) {
            $necesidades = ['Agua potable', 'Alimentos no perecederos', 'Transporte de carga', 'Medicamentos traslúcidos', 'Refugio temporal', 'Herramientas de zapa', 'EPP Forestal', 'Kit de higiene'];
            foreach ($necesidades as $nombre) {
                if ($db->table('necesidad')->where('nombre', $nombre)->exists()) continue;
                $db->table('necesidad')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. Solicitudes de Ayuda
        if (Schema::connection('seguimiento')->hasTable('solicitudes_ayuda')) {
            for ($i = 1; $i <= 25; $i++) {
                $db->table('solicitudes_ayuda')->insert([
                    'usuario_id' => $db->table('usuario')->pluck('id')->random(),
                    'necesidad_id' => $db->table('necesidad')->count() > 0 ? $db->table('necesidad')->pluck('id')->random() : null,
                    'descripcion' => 'Solicitud urgente de apoyo por contingencia ambiental #' . $i,
                    'estado' => ['pendiente', 'en_proceso', 'atendida', 'cancelada'][rand(0, 3)],
                    'ubicacion' => 'Coordenadas ' . rand(-17, -18) . '.' . rand(100, 999) . ', ' . rand(-62, -64) . '.' . rand(100, 999),
                    'created_at' => $now->copy()->subDays(rand(0, 30)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 5. Chat Mensajes
        if (Schema::connection('seguimiento')->hasTable('chat_mensajes')) {
            $mensajes = [
                'Brigada Alpha lista para salir a zona de Roboré.',
                'Necesitamos más suministros de agua en el punto de acopio 2.',
                'Confirmado el traslado de personal voluntario vía aérea.',
                'El fuego está controlado en el sector norte, procedemos con guardia de cenizas.',
                'Solicito reporte de situación del equipo en San Matías.',
                'Kit de primeros auxilios entregado con éxito.',
                'Hay un nuevo foco detectado cerca de la comunidad.',
                'Iniciando jornada de capacitación para nuevos voluntarios.'
            ];
            foreach ($mensajes as $mensaje) {
                $db->table('chat_mensajes')->insert([
                    'usuario_id' => $db->table('usuario')->pluck('id')->random(),
                    'mensaje' => $mensaje,
                    'created_at' => $now->subMinutes(rand(10, 5000)),
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Seguimiento: Datos demo de voluntarios y solicitudes ampliados significativamente.');

        $this->command?->info('Seguimiento: voluntarios, capacitaciones y actividad demo ampliados.');
    }
}
