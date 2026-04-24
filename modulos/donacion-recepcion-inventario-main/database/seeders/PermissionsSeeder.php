<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para guard 'web' solamente
        $permissions = [
            'ver-usuarios',
            'ver-campanas',
            'gestionar-campanas',
            'ver-puntos',
            'ver-categorias',
            'ver-productos',
            'ver-donantes',
            'ver-almacen',
            'gestionar-almacen',
            'registrar-donaciones',
            'consultar-donaciones',
            'consultar-inventario',
            'gestionar-solicitudes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Asignar permisos a roles
        $administrador = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();
        if ($administrador) {
            // Administrador tiene todos los permisos
            $administrador->syncPermissions(Permission::all());
        }

        $voluntario = Role::where('name', 'Voluntario')->where('guard_name', 'web')->first();
        if ($voluntario) {
            // Voluntario puede ver campañas, inventario, donaciones y crear solicitudes de recolección
            $voluntario->syncPermissions([
                'ver-campanas',
                'ver-donantes',
                'registrar-donaciones',
                'consultar-donaciones',
                'consultar-inventario',
                'gestionar-solicitudes',
            ]);
        }

        $almacenista = Role::where('name', 'Almacenista')->where('guard_name', 'web')->first();
        if ($almacenista) {
            // Almacenista tiene permisos de almacén y donaciones
            $almacenista->syncPermissions([
                'ver-almacen',
                'gestionar-almacen',
                'registrar-donaciones',
                'consultar-donaciones',
                'consultar-inventario',
                'gestionar-solicitudes',
            ]);
        }
    }
}



