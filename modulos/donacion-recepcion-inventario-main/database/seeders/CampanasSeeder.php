<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CampanasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campanas = [
            [
                'nombre' => 'Campaña Solidaria de Invierno',
                'descripcion' => 'Campaña para recolectar donaciones durante la temporada de invierno y ayudar a las familias más necesitadas.',
                'fecha_inicio' => Carbon::now()->subDays(30),
                'fecha_fin' => Carbon::now()->addDays(60),
                'imagen_banner' => 'https://unlp.edu.ar/wp-content/uploads/9/29509/6ef48a2568f9a8f22e42edfedce47972.jpg',
            ],
            [
                'nombre' => 'Campaña Navideña',
                'descripcion' => 'Recolecta de donaciones para celebrar la Navidad con las comunidades más vulnerables.',
                'fecha_inicio' => Carbon::now()->subDays(15),
                'fecha_fin' => Carbon::now()->addDays(45),
                'imagen_banner' => 'https://unlp.edu.ar/wp-content/uploads/10/29510/40fabb9c7c6a05ef03a41b472f722452.jpg',
            ],
        ];

        foreach ($campanas as $campana) {
            DB::table('campanas')->updateOrInsert(
                ['nombre' => $campana['nombre']],
                $campana
            );
        }
    }
}



