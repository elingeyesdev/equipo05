<?php

namespace Modules\Incendios\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\Simulacione;
use Modules\Incendios\Models\Prediction;

class VolunteerActivitySeeder extends Seeder
{
    /**
     * Seed actividades (incluye soft-deleted) para trazabilidad testing
     */
    public function run(): void
    {
        $ci = '1234567-0';

        $this->command->info("Creando actividades para CI: {$ci}");

        // Crear 3 biomasas y soft-delete 1
        $biomasas = [];
        for ($i = 1; $i <= 3; $i++) {
            $b = Biomasa::create([
                'user_id' => null,
                'ci_usuario' => $ci,
                'tipo_biomasa_id' => 1,
                'densidad' => ['Alta','Media','Baja'][($i-1)%3],
                'area_m2' => 1000 * $i,
                'perimetro_m' => 100 * $i,
                'coordenadas' => [[-17.7 + ($i*0.01), -60.7 + ($i*0.01)]],
                'descripcion' => "Biomasa prueba {$i}",
                'estado' => $i === 3 ? 'rechazada' : 'aprobada',
                'fecha_reporte' => now()->subDays($i),
            ]);
            $biomasas[] = $b;
            $this->command->info("Biomasa creada: {$b->id}");
        }

        // Soft-delete the second biomasa
        if (isset($biomasas[1])) {
            $biomasas[1]->delete();
            $this->command->info("Biomasa soft-deleted: {$biomasas[1]->id}");
        }

        // Crear 2 simulaciones y soft-delete 1
        $simulaciones = [];
        for ($i = 1; $i <= 2; $i++) {
            $s = Simulacione::create([
                'admin_id' => null,
                'ci_usuario' => $ci,
                'nombre' => "Simulacion prueba {$i}",
                'fecha' => now()->subDays($i),
                'duracion' => 60 * $i,
                'focos_activos' => $i,
                'num_voluntarios_enviados' => 2 * $i,
                'estado' => 'completada',
                'temperature' => 30 + $i,
                'humidity' => 20 + $i,
                'wind_speed' => 10 + $i,
                'wind_direction' => 180,
                'simulation_speed' => 1.0,
                'fire_risk' => 70 + ($i*2),
                'map_center_lat' => -17.75,
                'map_center_lng' => -60.74,
            ]);
            $simulaciones[] = $s;
            $this->command->info("Simulación creada: {$s->id}");
        }

        if (isset($simulaciones[0])) {
            $simulaciones[0]->delete();
            $this->command->info("Simulación soft-deleted: {$simulaciones[0]->id}");
        }

        // Crear 2 predicciones y soft-delete 1
        $preds = [];
        for ($i = 1; $i <= 2; $i++) {
            $p = Prediction::create([
                'user_id' => null,
                'ci_usuario' => $ci,
                'foco_incendio_id' => null,
                'predicted_at' => now()->subDays($i),
                'path' => [[ 'lat' => -17.75, 'lng' => -60.74, 'time' => 0 ]],
                'meta' => ['confidence' => 0.8 - ($i*0.05)],
            ]);
            $preds[] = $p;
            $this->command->info("Predicción creada: {$p->id}");
        }

        if (isset($preds[1])) {
            $preds[1]->delete();
            $this->command->info("Predicción soft-deleted: {$preds[1]->id}");
        }

        $this->command->info('Seeder VolunteerActivitySeeder completado.');
    }
}
