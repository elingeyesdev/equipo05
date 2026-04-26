<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class SpatieRoleSyncSeeder extends Seeder
{
    /**
     * Seed roles and sync existing users from legacy tables.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $voluntarioRole = Role::firstOrCreate(['name' => 'voluntario']);

        $this->command->info('Roles creados: administrador, voluntario');

        // Si no hay usuarios, solo crear los roles y salir
        if (User::count() === 0) {
            $this->command->info('No hay usuarios para sincronizar. Solo se crearon los roles.');
            return;
        }

        // Sync existing users based on legacy tables
        $users = User::with(['administrador', 'voluntario'])->get();
        
        $adminCount = 0;
        $voluntarioCount = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Check if user already has roles assigned
            if ($user->roles()->count() > 0) {
                $this->command->warn("Usuario {$user->email} ya tiene roles asignados, saltando...");
                $skipped++;
                continue;
            }

            // Assign role based on legacy table relationships
            if ($user->administrador) {
                $user->assignRole('administrador');
                $adminCount++;
                $this->command->info("✓ {$user->email} → administrador");
            } elseif ($user->voluntario) {
                $user->assignRole('voluntario');
                $voluntarioCount++;
                $this->command->info("✓ {$user->email} → voluntario");
            } else {
                $this->command->warn("⚠ Usuario {$user->email} no tiene perfil de administrador ni voluntario");
            }
        }

        $this->command->info("\n=== Resumen de Sincronización ===");
        $this->command->info("Administradores asignados: {$adminCount}");
        $this->command->info("Voluntarios asignados: {$voluntarioCount}");
        $this->command->info("Saltados (ya tenían roles): {$skipped}");
        $this->command->info("Total procesados: {$users->count()}");
    }
}
