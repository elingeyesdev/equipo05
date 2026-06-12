<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios demo: uno por cada rol operativo final.
 * Contraseña = parte local del email antes de @
 */
class AccessControlDemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['nombre' => 'Super', 'apellido' => 'Admin', 'email' => 'admin123@gmail.com', 'rol' => 'Administrador'],
            ['nombre' => 'Pedro', 'apellido' => 'Almacen', 'email' => 'almacen123@gmail.com', 'rol' => 'Almacenero'],
            ['nombre' => 'Olivia', 'apellido' => 'Incendios', 'email' => 'incendios123@gmail.com', 'rol' => 'Operador de Incendios'],
            ['nombre' => 'Luis', 'apellido' => 'Logistica', 'email' => 'logistica123@gmail.com', 'rol' => 'Coordinador Logístico'],
            ['nombre' => 'Valeria', 'apellido' => 'Voluntarios', 'email' => 'coordvol123@gmail.com', 'rol' => 'Coordinador de Voluntarios'],
            ['nombre' => 'Marco', 'apellido' => 'Cuadrilla', 'email' => 'cuadrilla123@gmail.com', 'rol' => 'Jefe de Cuadrilla'],
            ['nombre' => 'Rosa', 'apellido' => 'Rescatista', 'email' => 'rescatista123@gmail.com', 'rol' => 'Rescatista'],
            ['nombre' => 'Elena', 'apellido' => 'Veterinaria', 'email' => 'veterinario123@gmail.com', 'rol' => 'Veterinario'],
            ['nombre' => 'Tomás', 'apellido' => 'Cuidador', 'email' => 'cuidador123@gmail.com', 'rol' => 'Cuidador'],
            ['nombre' => 'Carlos', 'apellido' => 'Ayuda', 'email' => 'voluntario123@gmail.com', 'rol' => 'Voluntario'],
            ['nombre' => 'Juan', 'apellido' => 'Perez', 'email' => 'juan1232@gmail.com', 'rol' => 'Donante'],
            ['nombre' => 'Maria', 'apellido' => 'Ciudadana', 'email' => 'ciudadano123@gmail.com', 'rol' => 'Ciudadano'],
        ];

        foreach ($usuarios as $u) {
            $password = explode('@', $u['email'])[0];
            $email = strtolower($u['email']);

            $user = Usuario::query()->whereRaw('LOWER(email) = ?', [$email])->first();

            if (! $user) {
                $user = Usuario::create([
                    'nombre' => $u['nombre'],
                    'apellido' => $u['apellido'],
                    'email' => $email,
                    'contrasena' => Hash::make($password),
                    'telefono' => '70000000',
                    'activo' => true,
                ]);
            } else {
                if (! Hash::check($password, (string) $user->contrasena)) {
                    $user->update(['contrasena' => Hash::make($password)]);
                }
            }

            AccessControl::syncSingleRole($user, $u['rol']);
            $this->command?->info("Demo {$u['rol']}: {$email} | Pass: {$password}");
        }
    }
}
