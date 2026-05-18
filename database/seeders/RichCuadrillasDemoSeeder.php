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

        $cursos = [
            'Kardex combate incendios I',
            'Kardex combate incendios II',
            'Primeros auxilios en campo',
            'Uso de motobombas',
            'Líneas de fuego y cortafuegos',
            'Coordinación de cuadrilla',
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

        if (Schema::connection('cuadrillas')->hasTable('nivel_entrenamiento')) {
            foreach (['Básico', 'Intermedio', 'Avanzado', 'Instructor'] as $nombre) {
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

        if (Schema::connection('cuadrillas')->hasTable('inscrito')) {
            foreach ($cursoIds as $i => $cursoId) {
                for ($j = 1; $j <= 4; $j++) {
                    if ($db->table('inscrito')->where('id_curso', $cursoId)->count() >= 4) {
                        break;
                    }
                    $db->table('inscrito')->insert([
                        'id_curso' => $cursoId,
                        'created_at' => $now->copy()->subDays($i + $j),
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        if (Schema::connection('cuadrillas')->hasTable('equipo')) {
            foreach (['Brigada Alpha', 'Brigada Beta', 'Brigada Gamma', 'Unidad móvil 1'] as $nombre) {
                if ($db->table('equipo')->where('nombre', $nombre)->exists()) {
                    continue;
                }
                $db->table('equipo')->insert([
                    'nombre' => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::connection('cuadrillas')->hasTable('comunario')) {
            for ($c = 1; $c <= 15; $c++) {
                $nombre = 'Comunario demo '.$c;
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

        if (Schema::connection('cuadrillas')->hasTable('foco_calor')) {
            for ($f = 1; $f <= 20; $f++) {
                if ($db->table('foco_calor')->count() >= 25) {
                    break;
                }
                $db->table('foco_calor')->insert([
                    'latitud' => -17.5 - ($f * 0.01),
                    'longitud' => -63.1 - ($f * 0.008),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::connection('cuadrillas')->hasTable('noticia')) {
            foreach (['Simulacro regional exitoso', 'Nueva normativa de brigadas', 'Capacitación en Montero'] as $titulo) {
                if ($db->table('noticia')->where('titulo', $titulo)->exists()) {
                    continue;
                }
                $db->table('noticia')->insert([
                    'titulo' => $titulo,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command?->info('Cuadrillas: cursos, equipos, comunarios y focos demo ampliados.');
    }
}
