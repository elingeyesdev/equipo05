<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            try {
                DB::statement(
                    "SELECT setval(pg_get_serial_sequence('core.roles', 'id'), COALESCE((SELECT MAX(id) FROM core.roles), 1), true)"
                );
            } catch (\Throwable) {
                // sqlite u otro esquema
            }
        }

        $roles = [
            [
                'name' => 'Administrador',
                'descripcion' => 'Tiene acceso total a todos los módulos.',
            ],
            [
                'name' => 'Almacenero',
                'descripcion' => 'Encargado de inventario y almacenes.',
            ],
            [
                'name' => 'Reportes',
                'descripcion' => 'Visualiza reportes y métricas.',
            ],
            [
                'name' => 'Voluntario',
                'descripcion' => 'Apoya en la logística y actividades.',
            ],
            [
                'name' => 'Donante',
                'descripcion' => 'Usuario registrado para realizar donaciones.',
            ],
            [
                'name' => 'Ciudadano',
                'descripcion' => 'Usuario registrado en módulos comunitarios (rescate, reportes públicos).',
            ],
        ];

        foreach ($roles as $rol) {
            $existing = Role::query()
                ->where('name', $rol['name'])
                ->where('guard_name', 'web')
                ->first();

            if ($existing) {
                $existing->update(['descripcion' => $rol['descripcion']]);

                continue;
            }

            Role::query()->create([
                'name' => $rol['name'],
                'guard_name' => 'web',
                'descripcion' => $rol['descripcion'],
            ]);
        }
    }
}
