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

        // 1. Estados de Sistema
        if (Schema::connection('cuadrillas')->hasTable('estado_sistema')) {
            $estados = [
                ['nombre' => 'Pendiente', 'codigo' => 'pendiente', 'color' => '#ffa500', 'tabla' => 'reportes'],
                ['nombre' => 'Atendido', 'codigo' => 'atendido', 'color' => '#28a745', 'tabla' => 'reportes'],
                ['nombre' => 'Descartado', 'codigo' => 'descartado', 'color' => '#6c757d', 'tabla' => 'reportes'],
                ['nombre' => 'Activo', 'codigo' => 'activo', 'color' => '#28a745', 'tabla' => 'equipos'],
                ['nombre' => 'Inactivo', 'codigo' => 'inactivo', 'color' => '#dc3545', 'tabla' => 'equipos'],
                ['nombre' => 'En ruta', 'codigo' => 'en_ruta', 'color' => '#007bff', 'tabla' => 'equipos'],
            ];
            foreach ($estados as $est) {
                if ($db->table('estado_sistema')->where('codigo', $est['codigo'])->where('tabla', $est['tabla'])->exists()) {
                    continue;
                }
                $db->table('estado_sistema')->insert(array_merge($est, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // 2. Tipos de Incidente
        if (Schema::connection('cuadrillas')->hasTable('tipo_incidente')) {
            $tipos = ['Incendio Forestal', 'Incendio de Pastizal', 'Quema Controlada', 'Foco de Calor Sospechoso'];
            foreach ($tipos as $nombre) {
                if ($db->table('tipo_incidente')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('tipo_incidente')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. Niveles de Gravedad
        if (Schema::connection('cuadrillas')->hasTable('nivel_gravedad')) {
            $niveles = ['Bajo', 'Medio', 'Alto', 'Crítico'];
            foreach ($niveles as $nombre) {
                if ($db->table('nivel_gravedad')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('nivel_gravedad')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Obtener IDs de estado y catalogos para llaves foráneas
        $estadoEquiposIds = Schema::connection('cuadrillas')->hasTable('estado_sistema')
            ? $db->table('estado_sistema')->where('tabla', 'equipos')->pluck('id_estado_sistema')->toArray()
            : [];
        $estadoReportesIds = Schema::connection('cuadrillas')->hasTable('estado_sistema')
            ? $db->table('estado_sistema')->where('tabla', 'reportes')->pluck('id_estado_sistema')->toArray()
            : [];
        $tipoIncidenteIds = Schema::connection('cuadrillas')->hasTable('tipo_incidente')
            ? $db->table('tipo_incidente')->pluck('id_tipo_incidente')->toArray()
            : [];
        $nivelGravedadIds = Schema::connection('cuadrillas')->hasTable('nivel_gravedad')
            ? $db->table('nivel_gravedad')->pluck('id_nivel_gravedad')->toArray()
            : [];

        // 4. Cursos
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
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'id_curso');
            }
        }

        // 5. Niveles de Entrenamiento
        if (Schema::connection('cuadrillas')->hasTable('nivel_entrenamiento')) {
            foreach (['Aspirante', 'Combatiente Forestal', 'Jefe de Cuadrilla', 'Jefe de Incidente', 'Instructor Certificado', 'Observador'] as $nombre) {
                if ($db->table('nivel_entrenamiento')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('nivel_entrenamiento')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 6. Inscritos
        if (Schema::connection('cuadrillas')->hasTable('inscrito')) {
            foreach ($cursoIds as $cursoId) {
                for ($j = 1; $j <= rand(5, 12); $j++) {
                    $db->table('inscrito')->insert([
                        'id_curso' => $cursoId,
                        'created_at' => $now->copy()->subDays(rand(1, 60)),
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 7. Equipos (Brigadas)
        if (Schema::connection('cuadrillas')->hasTable('equipo')) {
            $equipos = ['Brigada Halcones', 'Fuerza Jaguar', 'Unidad Puma', 'Escuadrón Cóndor', 'Rescate Chiquitano', 'Guardianes del Bosque', 'Patrulla Pantanal', 'Brigada Centinela'];
            foreach ($equipos as $nombre) {
                if ($db->table('equipo')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $lat = -17.5 - (rand(1, 100) * 0.01);
                $lng = -63.1 - (rand(1, 100) * 0.008);
                $estId = !empty($estadoEquiposIds) ? $estadoEquiposIds[array_rand($estadoEquiposIds)] : null;

                $db->table('equipo')->insert([
                    'nombre' => $nombre,
                    'cantidad_integrantes' => rand(5, 15),
                    'latitud' => $lat,
                    'longitud' => $lng,
                    'estado_id' => $estId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 8. Comunarios de Apoyo
        if (Schema::connection('cuadrillas')->hasTable('comunario')) {
            for ($c = 1; $c <= 25; $c++) {
                $nombre = 'Comunario '.rand(1, 100);
                if ($db->table('comunario')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('comunario')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 9. Focos de Calor
        if (Schema::connection('cuadrillas')->hasTable('foco_calor')) {
            for ($f = 1; $f <= 30; $f++) {
                $db->table('foco_calor')->insert([
                    'latitud' => -17.5 - (rand(1, 100) * 0.01),
                    'longitud' => -63.1 - (rand(1, 100) * 0.008),
                    'created_at' => $now->copy()->subHours(rand(1, 168)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 10. Noticias
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
                if ($db->table('noticia')->where('titulo', $titulo)->exists()) {
                    continue;
                }
                $db->table('noticia')->insert([
                    'titulo' => $titulo,
                    'descripcion' => 'Detalle informativo sobre ' . strtolower($titulo) . ' en la región de la Chiquitania.',
                    'url' => 'https://www.alaschiquitanas.org/noticias/' . rand(100, 999),
                    'image' => 'https://images.unsplash.com/photo-1542382156909-9ae37b3f56fd?auto=format&fit=crop&w=600&q=80',
                    'date' => $now->copy()->subDays(rand(1, 10)),
                    'created_at' => $now->copy()->subDays(rand(1, 10)),
                    'updated_at' => $now,
                ]);
            }
        }

        // 11. Reportes (Datos enriquecidos para el mapa)
        if (Schema::connection('cuadrillas')->hasTable('reporte')) {
            $lugares = ['Roboré', 'San José de Chiquitos', 'San Ignacio de Velasco', 'Concepción', 'Santiago de Chiquitos', 'San Matías'];
            $reportantes = ['Carlos Mendoza', 'Ana Rojas', 'Juan de Dios', 'María Gutierrez', 'Luis Fernando', 'Patricia Ortiz'];

            for ($r = 1; $r <= 20; $r++) {
                $lat = -17.5 - (rand(1, 100) * 0.01);
                $lng = -63.1 - (rand(1, 100) * 0.008);

                $estId = !empty($estadoReportesIds) ? $estadoReportesIds[array_rand($estadoReportesIds)] : null;
                $tipoId = !empty($tipoIncidenteIds) ? $tipoIncidenteIds[array_rand($tipoIncidenteIds)] : null;
                $gravedadId = !empty($nivelGravedadIds) ? $nivelGravedadIds[array_rand($nivelGravedadIds)] : null;

                $db->table('reporte')->insert([
                    'titulo' => 'Reporte Situacional #' . rand(100, 999),
                    'nombre_reportante' => $reportantes[rand(0, count($reportantes) - 1)],
                    'telefono_contacto' => '7' . rand(1000000, 9999999),
                    'fecha_hora' => $now->copy()->subHours(rand(1, 72)),
                    'nombre_lugar' => $lugares[rand(0, count($lugares) - 1)],
                    'latitud' => $lat,
                    'longitud' => $lng,
                    'tipo_incidente_id' => $tipoId,
                    'gravedad_id' => $gravedadId,
                    'comentario_adicional' => 'Avistamiento de humo denso con peligro de propagación por fuertes vientos.',
                    'cant_bomberos' => rand(0, 8),
                    'cant_paramedicos' => rand(0, 3),
                    'cant_veterinarios' => rand(0, 2),
                    'cant_autoridades' => rand(0, 1),
                    'estado_id' => $estId,
                    'created_at' => $now->copy()->subHours(rand(1, 72)),
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Cuadrillas: Datos demo ampliados con coordenadas geográficas y catálogos de estado.');
    }
}
