<?php

namespace Modules\Incendios\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Incendios\Models\User;
use Modules\Incendios\Models\Administrador;
use Modules\Incendios\Models\Voluntario;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'telefono' => '70000001',
                'cedula_identidad' => '1234567',
            ]
        );

        Administrador::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'departamento' => 'Sistemas',
                'nivel_acceso' => 5,
                'activo' => true,
            ]
        );

        // Assign role using Spatie (evitar duplicados)
        if (!$adminUser->hasRole('administrador')) {
            $adminUser->assignRole('administrador');
        }

        // Crear segundo administrador
        $adminUser2 = User::firstOrCreate(
            ['email' => 'maria@demo.com'],
            [
                'name' => 'María González',
                'password' => Hash::make('password'),
                'telefono' => '70000002',
                'cedula_identidad' => '7654321',
            ]
        );

        Administrador::firstOrCreate(
            ['user_id' => $adminUser2->id],
            [
                'departamento' => 'Operaciones',
                'nivel_acceso' => 3,
                'activo' => true,
            ]
        );

        // Assign role using Spatie
        if (!$adminUser2->hasRole('administrador')) {
            $adminUser2->assignRole('administrador');
        }

        // Crear voluntarios
        $voluntarioUser1 = User::firstOrCreate(
            ['email' => 'juan@demo.com'],
            [
                'name' => 'Juan Pérez',
                'password' => Hash::make('password'),
                'telefono' => '70000003',
                'cedula_identidad' => '1111111',
            ]
        );

        Voluntario::firstOrCreate(
            ['user_id' => $voluntarioUser1->id],
            [
                'direccion' => 'Av. Principal 123',
                'ciudad' => 'San José de Chiquitos',
                'zona' => 'Centro',
                'notas' => 'Disponible fines de semana',
            ]
        );

        if (!$voluntarioUser1->hasRole('voluntario')) {
            $voluntarioUser1->assignRole('voluntario');
        }

        $voluntarioUser2 = User::firstOrCreate(
            ['email' => 'ana@demo.com'],
            [
                'name' => 'Ana López',
                'password' => Hash::make('password'),
                'telefono' => '70000004',
                'cedula_identidad' => '2222222',
            ]
        );

        Voluntario::firstOrCreate(
            ['user_id' => $voluntarioUser2->id],
            [
                'direccion' => 'Calle 15 de Abril 456',
                'ciudad' => 'San José de Chiquitos',
                'zona' => 'Norte',
                'notas' => 'Experiencia en combate de incendios',
            ]
        );

        if (!$voluntarioUser2->hasRole('voluntario')) {
            $voluntarioUser2->assignRole('voluntario');
        }

        $voluntarioUser3 = User::firstOrCreate(
            ['email' => 'carlos@demo.com'],
            [
                'name' => 'Carlos Ramírez',
                'password' => Hash::make('password'),
                'telefono' => '70000005',
                'cedula_identidad' => '3333333',
            ]
        );

        Voluntario::firstOrCreate(
            ['user_id' => $voluntarioUser3->id],
            [
                'direccion' => 'Barrio El Prado 789',
                'ciudad' => 'San José de Chiquitos',
                'zona' => 'Sur',
                'notas' => null,
            ]
        );

        if (!$voluntarioUser3->hasRole('voluntario')) {
            $voluntarioUser3->assignRole('voluntario');
        }

        $this->command->info('Datos de demostración creados exitosamente!');
        $this->command->info('Email: admin@demo.com | Password: password');
    }
}
