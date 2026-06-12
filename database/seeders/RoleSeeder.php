<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * @deprecated Use AccessControlSeeder. Se mantiene por compatibilidad con scripts existentes.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AccessControlSeeder::class);
    }
}
