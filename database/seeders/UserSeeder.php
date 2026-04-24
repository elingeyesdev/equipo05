<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $usuarios = [
            [
                'nombre' => 'Super', 
                'apellido' => 'Admin',
                'email' => 'admin123@gmail.com', 
                'rol' => 'Administrador'
            ],
            [
                'nombre' => 'Pedro', 
                'apellido' => 'Almacen',
                'email' => 'almacen123@gmail.com', 
                'rol' => 'Almacenero'
            ],
            [
                'nombre' => 'Ana', 
                'apellido' => 'Analista',
                'email' => 'reportes123@gmail.com', 
                'rol' => 'Reportes'
            ],
            [
                'nombre' => 'Carlos', 
                'apellido' => 'Ayuda',
                'email' => 'voluntario123@gmail.com', 
                'rol' => 'Voluntario'
            ],
             [
                'nombre' => 'Juan', 
                'apellido' => 'Perez',
                'email' => 'juan1232@gmail.com', 
                'rol' => 'Donante'
            ]
        ];

        foreach ($usuarios as $u) {
            // 1. Extraemos la contraseña del correo (lo que está antes del @)
            $passwordSinEncriptar = explode('@', $u['email'])[0];

            // 2. Creamos el usuario
            $nuevoUsuario = Usuario::create([
                'nombre'     => $u['nombre'],
                'apellido'   => $u['apellido'],
                'email'      => $u['email'],
                // AQUÍ SE ASIGNA LA CONTRASEÑA REAL:
                'contrasena' => Hash::make($passwordSinEncriptar), 
                'telefono'   => '70000000',
                'activo'     => true
            ]);

            // 3. Asignamos el rol
            $nuevoUsuario->assignRole($u['rol']);
            
            // (Opcional) Imprimimos en consola para que confirmes que se creó
            $this->command->info("Usuario: {$u['email']} | Pass: {$passwordSinEncriptar}");
        }
    }
}