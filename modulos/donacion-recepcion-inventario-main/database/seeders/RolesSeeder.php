<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiamos caché
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Creamos los roles usando 'name' que es lo que sale en tu diagrama
        $roles = [
            'Administrador',
            'Voluntario',
            'Almacenista',
        ];

        foreach ($roles as $rolName) {
            Role::firstOrCreate([
                'name' => $rolName,
                'guard_name' => 'web'
            ]);
        }
    }
}


