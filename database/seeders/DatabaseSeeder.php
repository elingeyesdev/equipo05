<?php

namespace Database\Seeders;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (UnifiedPostgres::enabled()) {
            $previous = config('database.default');
            config(['database.default' => UnifiedPostgres::coreAuthConnection()]);
            try {
                $this->call([
                    AccessControlSeeder::class,
                    AccessControlDemoUsersSeeder::class,
                ]);
            } finally {
                config(['database.default' => $previous]);
            }
        }

        $this->call([
            UnifiedDemoDataSeeder::class,
            RichUnifiedDemoSeeder::class,
        ]);
    }
}
