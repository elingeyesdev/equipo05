<?php

namespace Modules\Incendios\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\Simulacione;
use Modules\Incendios\Models\Prediction;

class SantaCruzSeeder extends Seeder
{
    /**
     * Seed sample activities located in Santa Cruz
     */
    public function run(): void
    {
        $ci = '1234567-0';

        $this->command->info("Creando actividades con ubicacion 'Santa Cruz' (CI: {$ci})");

        // Biomasas
        $b1 = Biomasa::create([
            'user_id' => null,
            'ci_usuario' => $ci,
            'tipo_biomasa_id' => 1,
            'densidad' => 'Alta',
            'area_m2' => 4200,
            'perimetro_m' => 360,
            'coordenadas' => [[-17.7833, -63.1821]],
            'descripcion' => 'Biomasa test Santa Cruz A',
            'ubicacion' => 'Santa Cruz',
            'estado' => 'aprobada',
            'fecha_reporte' => now()->subDays(3),
        ]);
        $this->command->info("Biomasa creada: {$b1->id}");

        $b2 = Biomasa::create([
            'user_id' => null,
            'ci_usuario' => $ci,
            'tipo_biomasa_id' => 1,
            'densidad' => 'Media',
            'area_m2' => 2600,
            'perimetro_m' => 240,
            'coordenadas' => [[-17.8000, -63.2000]],
            'descripcion' => 'Biomasa test Santa Cruz B',
            'ubicacion' => 'Santa Cruz',
            'estado' => 'pendiente',
            'fecha_reporte' => now()->subDays(5),
        ]);
        $this->command->info("Biomasa creada: {$b2->id}");

        $b3 = Biomasa::create([
            'user_id' => null,
            'ci_usuario' => $ci,
            'tipo_biomasa_id' => 1,
            'densidad' => 'Baja',
            'area_m2' => 1500,
            'perimetro_m' => 180,
            'coordenadas' => [[-17.7700, -63.1700]],
            'descripcion' => 'Biomasa test Santa Cruz C',
            'ubicacion' => 'Santa Cruz',
            'estado' => 'rechazada',
            'fecha_reporte' => now()->subDays(8),
        ]);
        $this->command->info("Biomasa creada: {$b3->id}");

        // Simulaciones
        $s1 = Simulacione::create([
            'admin_id' => null,
            'ci_usuario' => $ci,
            'nombre' => 'Simulación Santa Cruz - Escenario 1',
            'fecha' => now()->subDays(2),
            'duracion' => 90,
            'focos_activos' => 2,
            'num_voluntarios_enviados' => 6,
            'estado' => 'completada',
            'temperature' => 34.0,
            'humidity' => 22.0,
            'wind_speed' => 12.5,
            'wind_direction' => 200,
            'simulation_speed' => 1.0,
            'fire_risk' => 78,
            'map_center_lat' => -17.7833,
            'map_center_lng' => -63.1821,
        ]);
        $this->command->info("Simulación creada: {$s1->id}");

        $s2 = Simulacione::create([
            'admin_id' => null,
            'ci_usuario' => $ci,
            'nombre' => 'Simulación Santa Cruz - Vientos Fuertes',
            'fecha' => now()->subDays(6),
            'duracion' => 120,
            'focos_activos' => 4,
            'num_voluntarios_enviados' => 10,
            'estado' => 'completada',
            'temperature' => 36.0,
            'humidity' => 18.0,
            'wind_speed' => 25.0,
            'wind_direction' => 250,
            'simulation_speed' => 1.0,
            'fire_risk' => 85,
            'map_center_lat' => -17.8000,
            'map_center_lng' => -63.2000,
        ]);
        $this->command->info("Simulación creada: {$s2->id}");

        // Predicciones
        $p1 = Prediction::create([
            'user_id' => null,
            'ci_usuario' => $ci,
            'foco_incendio_id' => null,
            'predicted_at' => now()->subDays(1),
            'path' => [[ 'lat' => -17.7833, 'lng' => -63.1821, 'time' => 0, 'affected_area_km2' => 0.4 ]],
            'meta' => [ 'temperature' => 34, 'confidence_score' => 0.82, 'location' => 'Santa Cruz' ],
        ]);
        $this->command->info("Predicción creada: {$p1->id}");

        $p2 = Prediction::create([
            'user_id' => null,
            'ci_usuario' => $ci,
            'foco_incendio_id' => null,
            'predicted_at' => now()->subDays(4),
            'path' => [[ 'lat' => -17.8000, 'lng' => -63.2000, 'time' => 0, 'affected_area_km2' => 0.6 ]],
            'meta' => [ 'temperature' => 36, 'confidence_score' => 0.75, 'location' => 'Santa Cruz' ],
        ]);
        $this->command->info("Predicción creada: {$p2->id}");

        $this->command->info('Seeder SantaCruzSeeder completado.');
    }
}
