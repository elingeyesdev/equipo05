<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden
        $this->call([
            PermissionsSeeder::class,  // Crear permisos y asignarlos a roles
            AdminUsuarioSeeder::class, // Crear usuario administrador
        ]);

        $this->command->info('Database seeding completed successfully!');
    }
}



