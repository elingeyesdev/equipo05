<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupUnifiedDatabase extends Command
{
    protected $signature = 'db:setup-unificado
                            {--fresh-inventario : Reinicia esquema inventario y migra}
                            {--seed : Inserta datos demo en todos los modulos PG}';

    protected $description = 'Configura inventario (migraciones) y opcionalmente datos demo en PostgreSQL unificado';

    public function handle(): int
    {
        $fresh = $this->option('fresh-inventario') ? ['--fresh' => true] : [];

        if ($this->call('db:setup-inventario', $fresh) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($this->option('seed')) {
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\UnifiedDemoDataSeeder', '--force' => true]);
        }

        $this->info('Base unificada lista. Docker debe estar en ejecucion (puerto 5433).');

        return self::SUCCESS;
    }
}
