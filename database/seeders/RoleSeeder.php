<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Definimos todos los roles en un array para tener el código ordenado
        $roles = [
            // 1. TUS NUEVOS ROLES
            [
                'name' => 'Administrador', 
                'descripcion' => 'Tiene acceso total a todos los módulos.'
            ],
            [
                'name' => 'Almacenero',    
                'descripcion' => 'Encargado de inventario y almacenes.'
            ],
            [
                'name' => 'Reportes',      
                'descripcion' => 'Visualiza reportes y métricas.' 
            ],

            // 2. EL ROL VOLUNTARIO (Que pediste conservar)
            [
                'name' => 'Voluntario',    
                'descripcion' => 'Apoya en la logística y actividades.'
            ],

            // 3. EL ROL DONANTE (Importante: Lo usaste en tu UserSeeder anterior)
            [
                'name' => 'Donante',       
                'descripcion' => 'Usuario registrado para realizar donaciones.'
            ]
        ];

        // Recorremos y creamos
        foreach ($roles as $rol) {
            // firstOrCreate: Busca por 'name'. Si existe, no hace nada. Si no, lo crea.
            Role::firstOrCreate(
                ['name' => $rol['name']], 
                ['descripcion' => $rol['descripcion']]
            );
        }
    }
}