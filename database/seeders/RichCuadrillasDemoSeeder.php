<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RichCuadrillasDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('cuadrillas')->hasTable('curso')) {
            return;
        }

        $db = DB::connection('cuadrillas');
        $now = Carbon::now();

        // 1. Cursos
        $cursos = [
            'Combate de Incendios Nivel I', 'Combate de Incendios Nivel II', 'Primeros Auxilios en Áreas Remotas',
            'Soporte Vital Básico', 'Uso y Mantenimiento de Motobombas', 'Estrategias de Cortafuegos',
            'Coordinación de Incidente (ICS)', 'Supervivencia en el monte', 'Manejo de Herramientas Manuales',
            'Cartografía y Navegación Terrestre', 'Seguridad del Bombero Forestal', 'Meteorología para Incendios'
        ];

        $cursoIds = [];
        foreach ($cursos as $nombre) {
            $row = $db->table('curso')->where('nombre', $nombre)->first();
            if ($row) {
                $cursoIds[] = $row->id_curso;
            } else {
                $cursoIds[] = $db->table('curso')->insertGetId([
                    'nombre' => $nombre,
                    'descripcion' => 'Descripción detallada del curso de ' . $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'id_curso');
            }
        }

        // 2. Niveles de Entrenamiento
        if (Schema::connection('cuadrillas')->hasTable('nivel_entrenamiento')) {
            foreach (['Aspirante', 'Combatiente Forestal', 'Jefe de Cuadrilla', 'Jefe de Incidente', 'Instructor Certificado', 'Observador'] as $nombre) {
                if ($db->table('nivel_entrenamiento')->where('nombre', $nombre)->exists()) continue;
                $db->table('nivel_entrenamiento')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. Inscritos
        if (Schema::connection('cuadrillas')->hasTable('inscrito')) {
            foreach ($cursoIds as $cursoId) {
                for ($j = 1; $j <= rand(5, 12); $j++) {
                    $db->table('inscrito')->insert([
                        'id_curso' => $cursoId,
                        'nombre_completo' => 'Cursante '.rand(1, 100),
                        'ci' => rand(1000000, 9999999),
                        'estado' => ['aprobado', 'cursando', 'reprobado'][rand(0, 2)],
                        'created_at' => $now->copy()->subDays(rand(1, 60)),
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 4. Equipos (Brigadas)
        if (Schema::connection('cuadrillas')->hasTable('equipo')) {
            $equipos = ['Brigada Halcones', 'Fuerza Jaguar', 'Unidad Puma', 'Escuadrón Cóndor', 'Rescate Chiquitano', 'Guardianes del Bosque', 'Patrulla Pantanal', 'Brigada Centinela'];
            foreach ($equipos as $nombre) {
                if ($db->table('equipo')->where('nombre', $nombre)->exists()) continue;
                $db->table('equipo')->insert([
                    'nombre' => $nombre,
                    'zona_asignada' => 'Sector ' . ['Norte', 'Sur', 'Este', 'Oeste', 'Central'][rand(0, 4)],
                    'capacidad' => rand(5, 15) . ' personas',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 5. Comunarios de Apoyo
        if (Schema::connection('cuadrillas')->hasTable('comunario')) {
            for ($c = 1; $c <= 25; $c++) {
                $nombre = 'Comunario '.rand(1, 100);
                if ($db->table('comunario')->where('nombre', $nombre)->exists()) continue;
                $db->table('comunario')->insert([
                    'nombre' => $nombre,
                    'comunidad' => ['Roboré', 'San Matías', 'San Ignacio', 'Puerto Suárez'][rand(0, 3)],
                    'telefono' => '7'.rand(1000000, 9999999),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 6. Focos de Calor
        if (Schema::connection('cuadrillas')->hasTable('foco_calor')) {
            for ($f = 1; $f <= 30; $f++) {
                $db->table('foco_calor')->insert([
                    'latitud' => -17.5 - (rand(1, 100) * 0.01),
                    'longitud' => -63.1 - (rand(1, 100) * 0.008),
                    'intensidad' => rand(30, 100) . '%',
                    'created_at' => $now->subHours(rand(1, 168)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 7. Noticias
        if (Schema::connection('cuadrillas')->hasTable('noticia')) {
            $noticias = [
                'Alerta amarilla por vientos intensos',
                'Exitosa capacitación en San José de Chiquitos',
                'Nuevos equipos de respiración recibidos',
                'Brigada Halcones controló foco en km 50',
                'Se convoca a nuevos voluntarios para el mes de junio',
                'Reporte trimestral de incendios forestales listo'
            ];
            foreach ($noticias as $titulo) {
                if ($db->table('noticia')->where('titulo', $titulo)->exists()) continue;
                $db->table('noticia')->insert([
                    'titulo' => $titulo,
                    'contenido' => 'Cuerpo de la noticia referente a ' . $titulo,
                    'created_at' => $now->subDays(rand(1, 10)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 8. Reportes (Nuevos datos)
        if (Schema::connection('cuadrillas')->hasTable('reporte')) {
            for ($r = 1; $r <= 20; $r++) {
                $db->table('reporte')->insert([
                    'titulo' => 'Reporte Situacional #' . rand(100, 999),
                    'descripcion' => 'Informe de avance sobre la situación en el sector ' . $r,
                    'tipo' => ['informativo', 'operativo', 'emergencia'][rand(0, 2)],
                    'created_at' => $now->subHours(rand(1, 72)),
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Cuadrillas: Datos demo ampliados significativamente (mínimo 20 por tabla).');

        $this->command?->info('Cuadrillas: cursos, equipos, comunarios y focos demo ampliados.');
    }
}
