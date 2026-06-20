<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupUnifiedDatabase extends Command
{
    protected $signature = 'db:setup-unificado
                            {--fresh-inventario : Reinicia esquema inventario y migra}
                            {--seed : Inserta datos demo en todos los modulos PG}';

    protected $description = 'Alias de db:onboard para inventario + demo (PostgreSQL unificado)';

    public function handle(): int
    {
        $args = [];
        if ($this->option('fresh-inventario')) {
            $args['--fresh-inventario'] = true;
        }
        if ($this->option('seed')) {
            $args['--seed'] = true;
        }

        return $this->call('db:onboard', $args);
    }
}
