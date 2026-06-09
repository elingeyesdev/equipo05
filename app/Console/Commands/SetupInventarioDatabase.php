<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupInventarioDatabase extends Command
{
    protected $signature = 'db:setup-inventario
                            {--fresh : Borra el esquema inventario y vuelve a migrar}';

    protected $description = 'Crea tablas del modulo inventario/almacen en PostgreSQL (esquema inventario)';

    public function handle(): int
    {
        if (! filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL)) {
            $this->warn('DATABASE_UNIFIED_POSTGRES no esta en true. El modulo inventario usara SQLite.');

            return self::SUCCESS;
        }

        $driver = DB::connection('inventario')->getDriverName();
        if ($driver !== 'pgsql') {
            $this->error("Conexion inventario usa '{$driver}', se esperaba pgsql.");

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->info('Reiniciando esquema inventario...');
            $sql = base_path('database/unified_postgresql/04a_inventario_schema_reset.sql');
            DB::connection('inventario')->unprepared(file_get_contents($sql));
        }

        $this->info('Ejecutando migraciones del modulo inventario...');
        Artisan::call('migrate', [
            '--database' => 'inventario',
            '--path' => 'modulos/donacion-recepcion-inventario-main/database/migrations',
            '--force' => true,
        ], $this->output);

        $this->info(Artisan::output());

        $sync = app(\App\Services\Auth\CoreUserProvisioningService::class)->syncAllInventarioUsers();
        $this->info("Usuarios inventario sincronizados a core: {$sync['synced']}");

        $this->info('Listo. Prueba /inventario/home en el navegador.');

        return self::SUCCESS;
    }
}
