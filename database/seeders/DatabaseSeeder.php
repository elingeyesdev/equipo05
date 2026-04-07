<?php

namespace Database\Seeders;

use App\Models\Incendio;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        if (Incendio::query()->count() === 0) {
            Incendio::query()->insert([
                [
                    'titulo' => 'Incendio forestal en zona norte',
                    'descripcion' => 'Foco activo cercano a area residencial, requiere monitoreo continuo.',
                    'latitud' => -17.7720000,
                    'longitud' => -63.1820000,
                    'estado' => 'activo',
                    'nivel_riesgo' => 'alto',
                    'fecha_inicio' => now()->subHours(6),
                    'fecha_fin' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titulo' => 'Quema controlada en periferia',
                    'descripcion' => 'Incidente contenido parcialmente por brigadas locales.',
                    'latitud' => -17.7905000,
                    'longitud' => -63.1653000,
                    'estado' => 'controlado',
                    'nivel_riesgo' => 'medio',
                    'fecha_inicio' => now()->subHours(12),
                    'fecha_fin' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'titulo' => 'Incendio menor en pastizales',
                    'descripcion' => 'Evento en etapa final de extincion, bajo impacto.',
                    'latitud' => -17.8014000,
                    'longitud' => -63.1407000,
                    'estado' => 'extinguido',
                    'nivel_riesgo' => 'bajo',
                    'fecha_inicio' => now()->subDay(),
                    'fecha_fin' => now()->subHours(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
