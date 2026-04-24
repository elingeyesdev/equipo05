<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el rol de Administrador usando Spatie
        $rolAdmin = Role::where('name', 'Administrador')->where('guard_name', 'web')->first();

        if (!$rolAdmin) {
            $this->command->error('No existe el rol de Administrador. Ejecuta primero las migraciones de Spatie.');
            return;
        }

        // Verificar si ya existe el usuario
        $usuarioExistente = Usuario::where('correo', 'admin123456@gmail.com')->first();

        if ($usuarioExistente) {
            $this->command->info('El usuario administrador ya existe.');
            // Asegurarse de que tenga el rol asignado
            if (!$usuarioExistente->hasRole('Administrador')) {
                $usuarioExistente->assignRole('Administrador');
                $this->command->info('Rol de Administrador asignado al usuario existente.');
            }
            return;
        }

        // Crear usuario administrador
        $usuario = Usuario::create([
            'nombres' => 'Administrador',
            'apellidos' => 'Sistema',
            'ci' => 'ADMIN-2025',
            'foto_ci' => null,
            'licencia_conducir' => null,
            'foto_licencia' => null,
            'genero' => 'Otro',
            'correo' => 'admin123456@gmail.com',
            'telefono' => '00000000',
            'direccion_domicilio' => 'Oficina Central',
            'contrasena' => Hash::make('admin123456'),
            'estado' => 'Activo',
            'entidad_pertenencia' => null,
            'tipo_sangre' => null,
            'is_recolector' => false,
            'fecha_registro' => Carbon::now(),
        ]);

        // Asignar rol usando Spatie
        $usuario->assignRole('Administrador');

        $this->command->info('Usuario administrador creado exitosamente.');
        $this->command->info('Email: admin123456@gmail.com');
        $this->command->info('Contraseña: admin123456');
    }
}



