<?php

namespace Modules\Incendios\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'administrador',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'voluntario',
            'guard_name' => 'web',
        ]);
    }
}
