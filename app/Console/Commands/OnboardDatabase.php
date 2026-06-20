<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OnboardDatabase extends Command
{
    protected $signature = 'db:onboard
                            {--fresh-inventario : Reinicia esquema inventario antes de migrar}
                            {--seed : Inserta datos demo (roles, usuarios, modulos)}
                            {--skip-seed : No ejecuta seed aunque no haya admin}
                            {--wait-db=0 : Segundos maximo esperando PostgreSQL (0=sin espera)}';

    protected $description = 'Prepara la base PostgreSQL unificada para desarrollo (primer clone o volumen Docker nuevo)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->error('Requiere DATABASE_UNIFIED_POSTGRES=true en .env');
            $this->line('Para SQLite local sigue la seccion "Arranque local" del README.md');

            return self::FAILURE;
        }

        $wait = (int) $this->option('wait-db');
        if ($wait > 0 && ! $this->waitForDatabase($wait)) {
            return self::FAILURE;
        }

        if (! $this->verifyConnection()) {
            return self::FAILURE;
        }

        $this->components->info('Onboarding PostgreSQL unificado…');

        $steps = [
            ['config:clear', []],
            ['db:setup-transparencia', []],
            ['migrate', ['--force' => true]],
            ['rescate:ensure-schema', []],
            ['db:ensure-core-cache', []],
        ];

        foreach ($steps as [$command, $args]) {
            $this->components->task($command, fn () => $this->callSilent($command, $args) === self::SUCCESS);
        }

        $inventarioArgs = $this->option('fresh-inventario') ? ['--fresh' => true] : [];
        $this->components->task('db:setup-inventario', fn () => $this->callSilent('db:setup-inventario', $inventarioArgs) === self::SUCCESS);

        if ($this->shouldSeed()) {
            $this->components->task('db:seed', fn () => $this->callSilent('db:seed', ['--force' => true]) === self::SUCCESS);
        } else {
            $this->components->warn('Seed omitido (usa --seed para forzar).');
        }

        @Artisan::call('storage:link');
        $this->components->task('rescate:ensure-media', fn () => $this->callSilent('rescate:ensure-media', ['--sync-db' => true]) === self::SUCCESS);

        $this->newLine();
        $this->components->info('Base lista. Verifica con: php scripts/verify-unified-modules.php');
        $this->line('Login demo: admin123@gmail.com / admin123');

        return self::SUCCESS;
    }

    private function verifyConnection(): bool
    {
        try {
            DB::connection('core')->getPdo();
            if (DB::connection('core')->getDriverName() !== 'pgsql') {
                $this->error('DB_CONNECTION/core debe usar pgsql en modo unificado.');

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->error('No se pudo conectar a PostgreSQL: '.$e->getMessage());
            $this->line('Levanta la base: docker compose up -d db_unificado');
            $this->line('Revisa UNIFIED_PG_HOST/PORT en .env (Docker: puerto 5433).');

            return false;
        }
    }

    private function waitForDatabase(int $maxSeconds): bool
    {
        $this->components->info("Esperando PostgreSQL (max {$maxSeconds}s)…");
        $deadline = time() + $maxSeconds;

        while (time() < $deadline) {
            if ($this->verifyConnection()) {
                return true;
            }
            sleep(2);
        }

        $this->error('Tiempo de espera agotado.');

        return false;
    }

    private function shouldSeed(): bool
    {
        if ($this->option('skip-seed')) {
            return false;
        }

        if ($this->option('seed')) {
            return true;
        }

        return ! Usuario::query()
            ->whereRaw('LOWER(email) = ?', ['admin123@gmail.com'])
            ->exists();
    }
}
