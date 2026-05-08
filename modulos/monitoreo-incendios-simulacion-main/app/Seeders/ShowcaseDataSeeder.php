<?php

namespace Modules\Incendios\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Incendios\Models\Administrador;
use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\FocosIncendio;
use Modules\Incendios\Models\Prediction;
use Modules\Incendios\Models\Simulacione;
use Modules\Incendios\Models\TipoBiomasa;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Voluntario;
use Spatie\Permission\Models\Role;

class ShowcaseDataSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'administrador', 'guard_name' => '']);
        Role::firstOrCreate(['name' => 'voluntario', 'guard_name' => '']);

        $pastizal = TipoBiomasa::firstOrCreate(
            ['tipo_biomasa' => 'Pastizal'],
            ['color' => '#90EE90', 'modificador_intensidad' => 1.0]
        );
        $bosque = TipoBiomasa::firstOrCreate(
            ['tipo_biomasa' => 'Bosque'],
            ['color' => '#006400', 'modificador_intensidad' => 1.5]
        );
        $agricola = TipoBiomasa::firstOrCreate(
            ['tipo_biomasa' => 'Agrícola'],
            ['color' => '#FFD700', 'modificador_intensidad' => 0.8]
        );

        $adminUser = User::withoutEvents(function () {
            return User::firstOrCreate(
                ['email' => 'admin.incendios.demo@sipii.local'],
                [
                    'name' => 'Coordinador Incendios Demo',
                    'password' => Hash::make('incendios123'),
                    'telefono' => '73310001',
                    'cedula_identidad' => '8900001',
                ]
            );
        });
        $adminProfile = Administrador::firstOrCreate(
            ['user_id' => $adminUser->id],
            ['departamento' => 'Gestión de riesgo', 'nivel_acceso' => 'completo', 'activo' => true]
        );

        $voluntarioAUser = User::withoutEvents(function () {
            return User::firstOrCreate(
                ['email' => 'voluntario.a.demo@sipii.local'],
                [
                    'name' => 'Daniel Rojas',
                    'password' => Hash::make('incendios123'),
                    'telefono' => '73310002',
                    'cedula_identidad' => '8900002',
                ]
            );
        });
        Voluntario::firstOrCreate(
            ['user_id' => $voluntarioAUser->id],
            ['direccion' => 'Barrio Plan 3000', 'ciudad' => 'Santa Cruz', 'zona' => 'Sur', 'notas' => 'Brigada nocturna']
        );

        $voluntarioBUser = User::withoutEvents(function () {
            return User::firstOrCreate(
                ['email' => 'voluntario.b.demo@sipii.local'],
                [
                    'name' => 'Gabriela Peña',
                    'password' => Hash::make('incendios123'),
                    'telefono' => '73310003',
                    'cedula_identidad' => '8900003',
                ]
            );
        });
        Voluntario::firstOrCreate(
            ['user_id' => $voluntarioBUser->id],
            ['direccion' => 'Av. Santos Dumont', 'ciudad' => 'Santa Cruz', 'zona' => 'Norte', 'notas' => 'Disponibilidad fines de semana']
        );

        $focoNorte = FocosIncendio::firstOrCreate(
            ['ubicacion' => 'San José de Chiquitos - Sector Norte'],
            ['fecha' => now()->subHours(10), 'coordenadas' => [-17.7532, -60.7123], 'intensidad' => 8.1]
        );
        $focoCentro = FocosIncendio::firstOrCreate(
            ['ubicacion' => 'Santa Cruz - Zona de amortiguación'],
            ['fecha' => now()->subHours(6), 'coordenadas' => [-17.7833, -63.1821], 'intensidad' => 6.3]
        );
        $focoEste = FocosIncendio::firstOrCreate(
            ['ubicacion' => 'Pailón - Franja Este'],
            ['fecha' => now()->subHours(3), 'coordenadas' => [-17.6471, -62.7330], 'intensidad' => 7.0]
        );

        Biomasa::firstOrCreate(
            ['descripcion' => 'Cobertura de pastizal seco en perímetro de riesgo norte'],
            [
                'fecha_reporte' => now()->subDays(2),
                'tipo_biomasa_id' => $pastizal->id,
                'area_m2' => 4800,
                'perimetro_m' => 340,
                'densidad' => 'Alta',
                'ubicacion' => 'San José de Chiquitos',
                'coordenadas' => [[-17.7540, -60.7140], [-17.7552, -60.7151], [-17.7538, -60.7162]],
                'user_id' => $adminUser->id,
                'ci_usuario' => $adminUser->cedula_identidad,
                'estado' => 'aprobada',
                'aprobada_por' => $adminUser->id,
                'fecha_revision' => now()->subDays(1),
            ]
        );

        Biomasa::firstOrCreate(
            ['descripcion' => 'Bosque bajo con material combustible acumulado'],
            [
                'fecha_reporte' => now()->subDays(1),
                'tipo_biomasa_id' => $bosque->id,
                'area_m2' => 6200,
                'perimetro_m' => 410,
                'densidad' => 'Media',
                'ubicacion' => 'Pailón',
                'coordenadas' => [[-17.6480, -62.7340], [-17.6490, -62.7352], [-17.6475, -62.7361]],
                'user_id' => $voluntarioAUser->id,
                'ci_usuario' => $voluntarioAUser->cedula_identidad,
                'estado' => 'pendiente',
            ]
        );

        Biomasa::firstOrCreate(
            ['descripcion' => 'Área agrícola con cortafuegos incompletos'],
            [
                'fecha_reporte' => now()->subDays(4),
                'tipo_biomasa_id' => $agricola->id,
                'area_m2' => 3500,
                'perimetro_m' => 260,
                'densidad' => 'Baja',
                'ubicacion' => 'Cuatro Cañadas',
                'coordenadas' => [[-17.5001, -62.8962], [-17.5014, -62.8970], [-17.4996, -62.8982]],
                'user_id' => $voluntarioBUser->id,
                'ci_usuario' => $voluntarioBUser->cedula_identidad,
                'estado' => 'rechazada',
                'motivo_rechazo' => 'Coordenadas iniciales incompletas, requiere nuevo relevamiento.',
                'aprobada_por' => $adminUser->id,
                'fecha_revision' => now()->subDays(3),
            ]
        );

        $simNorte = Simulacione::firstOrCreate(
            ['nombre' => 'Simulación Norte - Viento moderado'],
            [
                'fecha' => now()->subDays(2),
                'duracion' => 110,
                'focos_activos' => 3,
                'num_voluntarios_enviados' => 7,
                'estado' => 'completada',
                'admin_id' => $adminProfile->id,
                'ci_usuario' => $adminUser->cedula_identidad,
                'temperature' => 34.8,
                'humidity' => 24.0,
                'wind_speed' => 15.2,
                'wind_direction' => 210,
                'simulation_speed' => 1.0,
                'fire_risk' => 79,
                'map_center_lat' => -17.7532,
                'map_center_lng' => -60.7123,
                'public' => true,
                'initial_fires' => [['lat' => -17.7532, 'lng' => -60.7123, 'intensity' => 0.8]],
                'mitigation_strategies' => ['cortafuegos' => true, 'brigadas' => 3],
                'auto_stopped' => false,
            ]
        );
        $simNorte->focos()->syncWithoutDetaching([$focoNorte->id, $focoCentro->id]);

        $simEste = Simulacione::firstOrCreate(
            ['nombre' => 'Simulación Este - Alta propagación'],
            [
                'fecha' => now()->subDays(1),
                'duracion' => 135,
                'focos_activos' => 4,
                'num_voluntarios_enviados' => 10,
                'estado' => 'completada',
                'admin_id' => $adminProfile->id,
                'ci_usuario' => $adminUser->cedula_identidad,
                'temperature' => 36.2,
                'humidity' => 19.0,
                'wind_speed' => 22.4,
                'wind_direction' => 250,
                'simulation_speed' => 1.0,
                'fire_risk' => 88,
                'map_center_lat' => -17.6471,
                'map_center_lng' => -62.7330,
                'public' => true,
                'initial_fires' => [['lat' => -17.6471, 'lng' => -62.7330, 'intensity' => 0.92]],
                'mitigation_strategies' => ['cortafuegos' => true, 'apoyo_aereo' => true],
                'auto_stopped' => false,
            ]
        );
        $simEste->focos()->syncWithoutDetaching([$focoEste->id]);

        Prediction::firstOrCreate(
            ['ci_usuario' => $adminUser->cedula_identidad, 'foco_incendio_id' => $focoNorte->id],
            [
                'user_id' => $adminUser->id,
                'predicted_at' => now()->subHours(5),
                'path' => [
                    ['lat' => -17.7532, 'lng' => -60.7123, 'time' => 0, 'affected_area_km2' => 0.7],
                    ['lat' => -17.7540, 'lng' => -60.7140, 'time' => 2, 'affected_area_km2' => 1.5],
                    ['lat' => -17.7550, 'lng' => -60.7158, 'time' => 4, 'affected_area_km2' => 2.3],
                ],
                'meta' => ['fire_risk_index' => 82, 'confidence_score' => 0.84, 'source' => 'showcase-seeder'],
            ]
        );

        Prediction::firstOrCreate(
            ['ci_usuario' => $adminUser->cedula_identidad, 'foco_incendio_id' => $focoEste->id],
            [
                'user_id' => $adminUser->id,
                'predicted_at' => now()->subHours(2),
                'path' => [
                    ['lat' => -17.6471, 'lng' => -62.7330, 'time' => 0, 'affected_area_km2' => 0.9],
                    ['lat' => -17.6484, 'lng' => -62.7346, 'time' => 2, 'affected_area_km2' => 2.0],
                    ['lat' => -17.6498, 'lng' => -62.7360, 'time' => 4, 'affected_area_km2' => 3.1],
                ],
                'meta' => ['fire_risk_index' => 89, 'confidence_score' => 0.8, 'source' => 'showcase-seeder'],
            ]
        );
    }
}
