<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureCoreCacheTablesCommand extends Command
{
    protected $signature = 'db:ensure-core-cache';

    protected $description = 'Crea tablas cache/cache_locks en el esquema core (PostgreSQL unificado)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->warn('Solo aplica con DATABASE_UNIFIED_POSTGRES=true');

            return self::SUCCESS;
        }

        $schema = Schema::connection('core');

        if (! $schema->hasTable('cache')) {
            $schema->create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration')->index();
            });
            $this->info('Tabla core.cache creada.');
        }

        if (! $schema->hasTable('cache_locks')) {
            $schema->create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration')->index();
            });
            $this->info('Tabla core.cache_locks creada.');
        }

        config(['cache.stores.database.connection' => 'core']);

        return self::SUCCESS;
    }
}
