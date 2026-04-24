<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AlmacenistaUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el rol de Almacenista usando Spatie
        $rolAlmacenista = \Spatie\Permission\Models\Role::where('name', 'Almacenista')->where('guard_name', 'web')->first();

        if (!$rolAlmacenista) {
            $this->command->error('No existe el rol de Almacenista. Ejecuta primero el PermissionsSeeder.');
            return;
        }

        // Verificar si ya existe el usuario
        $usuarioExistente = Usuario::where('correo', 'almacenista@donaciones.com')->first();

        if ($usuarioExistente) {
            $this->command->info('El usuario almacenista ya existe.');
            return;
        }

        // Crear usuario almacenista
        Usuario::create([
            'nombres' => 'Carlos',
            'apellidos' => 'Rodríguez',
            'ci' => '5555555',
            'foto_ci' => null,
            'licencia_conducir' => null,
            'foto_licencia' => null,
            'genero' => 'Masculino',
            'correo' => 'almacenista@donaciones.com',
            'telefono' => '70123456',
            'direccion_domicilio' => 'Zona Central',
            'contrasena' => Hash::make('almacen123'),
            'estado' => 'Activo',
            'entidad_pertenencia' => null,
            'tipo_sangre' => null,
            'is_recolector' => false,
            'fecha_registro' => Carbon::now(),
        ]);

        // Asignar rol usando Spatie
        $usuario = Usuario::where('correo', 'almacenista@donaciones.com')->first();
        $usuario->assignRole('Almacenista');

        $this->command->info('Usuario almacenista creado exitosamente.');
        $this->command->info('Email: almacenista@donaciones.com');
        $this->command->info('Contraseña: almacen123');
    }
}



