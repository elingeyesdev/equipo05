<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Database\Seeders\RichUnifiedDemoSeeder;
use Illuminate\Console\Command;

class SeedRichDemoCommand extends Command
{
    protected $signature = 'db:seed-rich-demo';

    protected $description = 'Carga datos demo abundantes en los 7 módulos (PostgreSQL unificado)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->error('Requiere DATABASE_UNIFIED_POSTGRES=true en .env');

            return self::FAILURE;
        }

        $this->call('storage:link');

        $seeder = new RichUnifiedDemoSeeder;
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('Listo. Inicia sesión con admin123@gmail.com / admin123');

        return self::SUCCESS;
    }
}
