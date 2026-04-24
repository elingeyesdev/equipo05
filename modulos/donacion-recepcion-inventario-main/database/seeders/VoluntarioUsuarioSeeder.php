<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VoluntarioUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el rol de Voluntario usando Spatie
        $rolVoluntario = \Spatie\Permission\Models\Role::where('name', 'Voluntario')->where('guard_name', 'web')->first();

        if (!$rolVoluntario) {
            $this->command->error('No existe el rol de Voluntario. Ejecuta primero el PermissionsSeeder.');
            return;
        }

        // Verificar si ya existe el usuario
        $usuarioExistente = Usuario::where('correo', 'voluntario@donaciones.com')->first();

        if ($usuarioExistente) {
            $this->command->info('El usuario voluntario ya existe.');
            return;
        }

        // Crear usuario voluntario
        Usuario::create([
            'nombres' => 'María',
            'apellidos' => 'González',
            'ci' => '9876543',
            'foto_ci' => null,
            'licencia_conducir' => null,
            'foto_licencia' => null,
            'genero' => 'Femenino',
            'correo' => 'voluntario@donaciones.com',
            'telefono' => '71234567',
            'direccion_domicilio' => 'Zona Norte',
            'contrasena' => Hash::make('voluntario123'),
            'estado' => 'Activo',
            'entidad_pertenencia' => null,
            'tipo_sangre' => null,
            'is_recolector' => false,
            'fecha_registro' => Carbon::now(),
        ]);

        // Asignar rol usando Spatie
        $usuario = Usuario::where('correo', 'voluntario@donaciones.com')->first();
        $usuario->assignRole('Voluntario');

        $this->command->info('Usuario voluntario creado exitosamente.');
        $this->command->info('Email: voluntario@donaciones.com');
        $this->command->info('Contraseña: voluntario123');
    }
}



