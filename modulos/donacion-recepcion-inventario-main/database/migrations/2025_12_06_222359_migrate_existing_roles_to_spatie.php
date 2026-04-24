<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Role;
use App\Models\Usuario;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing roles from 'roles' table to Spatie's 'spatie_roles' table
        $existingRoles = \DB::table('roles')->get();

        foreach ($existingRoles as $role) {
            \DB::table('spatie_roles')->insert([
                'name' => $role->nombre_rol,
                'guard_name' => 'web', // Solo web, no afecta API
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign roles to existing users
        $usuarios = \DB::table('usuarios')->whereNotNull('id_rol')->get();

        foreach ($usuarios as $usuario) {
            // Get the role name from the old roles table
            $rol = \DB::table('roles')->where('id_rol', $usuario->id_rol)->first();
            if ($rol) {
                // Get the Spatie role
                $spatieRole = \DB::table('spatie_roles')->where('name', $rol->nombre_rol)->first();
                if ($spatieRole) {
                    // Insert into model_has_roles pivot table
                    \DB::table('model_has_roles')->insert([
                        'role_id' => $spatieRole->id,
                        'model_type' => 'App\Models\Usuario',
                        'model_id' => $usuario->id_usuario,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all Spatie roles and role assignments
        \DB::table('model_has_roles')->truncate();
        SpatieRole::where('guard_name', 'web')->delete();
    }
};



