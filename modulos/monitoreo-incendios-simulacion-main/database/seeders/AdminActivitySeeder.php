<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Biomasa;
use App\Models\Simulacione;
use App\Models\Prediction;
use App\Models\TipoBiomasa;

class AdminActivitySeeder extends Seeder
{
    /**
     * Seed actividades del administrador para testing de trazabilidad
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@sipii.com')->first();
        
        if (!$admin) {
            $this->command->error('Usuario admin@sipii.com no encontrado');
            return;
        }

        $this->command->info("Creando actividades para {$admin->name} (CI: {$admin->cedula_identidad})");

        // Obtener tipos de biomasa
        $tiposBiomasa = TipoBiomasa::all();
        
        if ($tiposBiomasa->isEmpty()) {
            $this->command->error('No hay tipos de biomasa. Ejecuta TipoBiomasaSeeder primero.');
            return;
        }

        // Crear 3 biomasas aprobadas
        $biomasasData = [
            [
                'tipo' => $tiposBiomasa->first()->id,
                'densidad' => 'Alta',
                'area' => 5000,
                'perimetro' => 300,
                'coords' => [[-17.7486, -60.7464], [-17.7496, -60.7474], [-17.7506, -60.7464], [-17.7486, -60.7464]],
                'descripcion' => 'Biomasa en zona norte - evaluación inicial',
                'estado' => 'aprobada',
            ],
            [
                'tipo' => $tiposBiomasa->skip(1)->first()->id ?? $tiposBiomasa->first()->id,
                'densidad' => 'Media',
                'area' => 3500,
                'perimetro' => 250,
                'coords' => [[-17.7586, -60.7564], [-17.7596, -60.7574], [-17.7606, -60.7564], [-17.7586, -60.7564]],
                'descripcion' => 'Área de pastizales controlada',
                'estado' => 'aprobada',
            ],
            [
                'tipo' => $tiposBiomasa->first()->id,
                'densidad' => 'Baja',
                'area' => 2000,
                'perimetro' => 180,
                'coords' => [[-17.7686, -60.7664], [-17.7696, -60.7674], [-17.7706, -60.7664], [-17.7686, -60.7664]],
                'descripcion' => 'Zona de monitoreo permanente',
                'estado' => 'pendiente',
            ],
        ];

        foreach ($biomasasData as $data) {
            $biomasa = Biomasa::create([
                'user_id' => $admin->id,
                'ci_usuario' => $admin->cedula_identidad,
                'tipo_biomasa_id' => $data['tipo'],
                'densidad' => $data['densidad'],
                'area_m2' => $data['area'],
                'perimetro_m' => $data['perimetro'],
                'coordenadas' => $data['coords'],
                'descripcion' => $data['descripcion'],
                'estado' => $data['estado'],
                'fecha_reporte' => now()->subDays(rand(1, 10)),
                'aprobada_por' => $data['estado'] === 'aprobada' ? $admin->id : null,
                'fecha_revision' => $data['estado'] === 'aprobada' ? now()->subDays(rand(0, 5)) : null,
            ]);
            
            $this->command->info("✓ Biomasa creada: {$biomasa->id} - {$data['descripcion']}");
        }

        // Crear 2 simulaciones
        $simulacionesData = [
            [
                'nombre' => 'Simulación Zona Norte - Escenario Alto Riesgo',
                'duracion' => 120,
                'focos_activos' => 5,
                'num_voluntarios' => 8,
                'temp' => 35.5,
                'humidity' => 25.0,
                'wind_speed' => 15.5,
                'wind_direction' => 180,
            ],
            [
                'nombre' => 'Simulación Área Central - Vientos Fuertes',
                'duracion' => 90,
                'focos_activos' => 3,
                'num_voluntarios' => 5,
                'temp' => 32.0,
                'humidity' => 30.0,
                'wind_speed' => 20.0,
                'wind_direction' => 270,
            ],
        ];

        foreach ($simulacionesData as $data) {
            $simulacion = Simulacione::create([
                'admin_id' => $admin->id,
                'ci_usuario' => $admin->cedula_identidad,
                'nombre' => $data['nombre'],
                'fecha' => now()->subDays(rand(1, 15)),
                'duracion' => $data['duracion'],
                'focos_activos' => $data['focos_activos'],
                'num_voluntarios_enviados' => $data['num_voluntarios'],
                'estado' => 'completada',
                'temperature' => $data['temp'],
                'humidity' => $data['humidity'],
                'wind_speed' => $data['wind_speed'],
                'wind_direction' => $data['wind_direction'],
                'simulation_speed' => 1.0,
                // `fire_risk` is an integer percent in the DB (e.g. 75)
                'fire_risk' => rand(60, 95),
                'map_center_lat' => -17.7486,
                'map_center_lng' => -60.7464,
            ]);
            
            $this->command->info("✓ Simulación creada: {$simulacion->id} - {$data['nombre']}");
        }

        // Crear 2 predicciones
        $prediccionesData = [
            [
                'path' => [
                    ['lat' => -17.7486, 'lng' => -60.7464, 'time' => 0, 'affected_area_km2' => 0.5],
                    ['lat' => -17.7496, 'lng' => -60.7474, 'time' => 2, 'affected_area_km2' => 1.2],
                    ['lat' => -17.7506, 'lng' => -60.7484, 'time' => 4, 'affected_area_km2' => 2.0],
                ],
                'meta' => [
                    'input_parameters' => [
                        'temperature' => 34.5,
                        'humidity' => 28.0,
                        'wind_speed' => 12.5,
                        'wind_direction' => 180,
                    ],
                    'fire_risk_index' => 75,
                    'confidence_score' => 0.85,
                    'prediction_method' => 'neural_network',
                ],
            ],
            [
                'path' => [
                    ['lat' => -17.7586, 'lng' => -60.7564, 'time' => 0, 'affected_area_km2' => 0.3],
                    ['lat' => -17.7596, 'lng' => -60.7574, 'time' => 2, 'affected_area_km2' => 0.8],
                    ['lat' => -17.7606, 'lng' => -60.7584, 'time' => 4, 'affected_area_km2' => 1.5],
                ],
                'meta' => [
                    'input_parameters' => [
                        'temperature' => 36.0,
                        'humidity' => 22.0,
                        'wind_speed' => 18.0,
                        'wind_direction' => 270,
                    ],
                    'fire_risk_index' => 82,
                    'confidence_score' => 0.78,
                    'prediction_method' => 'neural_network',
                ],
            ],
        ];

        foreach ($prediccionesData as $data) {
            $prediction = Prediction::create([
                'user_id' => $admin->id,
                'ci_usuario' => $admin->cedula_identidad,
                'foco_incendio_id' => null,
                'predicted_at' => now()->subDays(rand(1, 8)),
                'path' => $data['path'],
                'meta' => $data['meta'],
            ]);
            
            $this->command->info("✓ Predicción creada: {$prediction->id}");
        }

        $this->command->info("\n=== Resumen de Actividades Creadas ===");
        $this->command->info("Usuario: {$admin->name}");
        $this->command->info("CI: {$admin->cedula_identidad}");
        $this->command->info("Biomasas: 3");
        $this->command->info("Simulaciones: 2");
        $this->command->info("Predicciones: 2");
        $this->command->info("Total: 7 actividades");
    }
}
