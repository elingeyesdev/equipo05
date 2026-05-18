<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SetupTransparenciaCommand extends Command
{
    protected $signature = 'db:setup-transparencia {--force : Re-ejecuta el SQL aunque existan tablas}';

    protected $description = 'Crea el esquema transparencia y tablas campanias/donaciones (PostgreSQL unificado)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->error('Requiere DATABASE_UNIFIED_POSTGRES=true');

            return self::FAILURE;
        }

        $pg = DB::connection('core');
        if ($pg->getDriverName() !== 'pgsql') {
            $this->error('Requiere conexion PostgreSQL unificada.');

            return self::FAILURE;
        }

        $pg->statement('CREATE SCHEMA IF NOT EXISTS transparencia');

        $exists = $pg->selectOne(
            "SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = 'transparencia' AND table_name = 'campanias'
            ) AS e"
        );

        if (($exists->e ?? false) && ! $this->option('force')) {
            $this->info('transparencia.campanias ya existe.');

            return self::SUCCESS;
        }

        $path = database_path('unified_postgresql/04_mod_inventario_transparencia.sql');
        if (! File::isFile($path)) {
            $this->error('No se encontro 04_mod_inventario_transparencia.sql');

            return self::FAILURE;
        }

        $sql = File::get($path);
        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql) ?? $sql;
        $pg->unprepared($sql);
        $this->info('Esquema transparencia aplicado (campanias, donaciones, mensajes, etc.).');

        return self::SUCCESS;
    }
}
