<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;

class PostPullDatabase extends Command
{
    protected $signature = 'db:post-pull
                            {--seed : Re-ejecuta seeders si hace falta demo}';

    protected $description = 'Aplica cambios de esquema tras git pull (sin borrar datos)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->warn('Modo SQLite: ejecuta php artisan migrate --force y migraciones por modulo (ver README).');

            return $this->call('migrate', ['--force' => true]) === self::SUCCESS
                ? self::SUCCESS
                : self::FAILURE;
        }

        $this->components->info('Actualizando esquema tras pull…');

        $steps = [
            ['config:clear', []],
            ['migrate', ['--force' => true]],
            ['rescate:ensure-schema', []],
            ['db:ensure-core-cache', []],
            ['db:setup-inventario', []],
            ['rescate:ensure-media', ['--sync-db' => true]],
        ];

        if ($this->option('seed')) {
            $steps[] = ['db:seed', ['--force' => true]];
        }

        foreach ($steps as [$command, $args]) {
            $this->components->task($command, fn () => $this->callSilent($command, $args) === self::SUCCESS);
        }

        $this->newLine();
        $this->components->info('Listo. Opcional: php scripts/verify-unified-modules.php');

        return self::SUCCESS;
    }
}
