<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureRescateSchema extends Command
{
    protected $signature = 'rescate:ensure-schema';

    protected $description = 'Aplica parches de esquema rescate en PostgreSQL unificado (idempotente)';

    public function handle(): int
    {
        if (! Schema::connection('rescate')->hasTable('animal_histories')) {
            $this->components->warn('Tabla animal_histories no encontrada; omitiendo parches.');

            return self::SUCCESS;
        }

        if (DB::connection('rescate')->getDriverName() !== 'pgsql') {
            return self::SUCCESS;
        }

        if ($this->animalFileIdIsNullable()) {
            $this->components->info('Esquema rescate OK (animal_histories.animal_file_id nullable).');

            return self::SUCCESS;
        }

        $path = database_path('unified_postgresql/03c_animal_histories_nullable.sql');
        if (! is_file($path)) {
            $this->components->error('Falta database/unified_postgresql/03c_animal_histories_nullable.sql');

            return self::FAILURE;
        }

        DB::connection('rescate')->unprepared((string) file_get_contents($path));
        $this->components->info('Parche aplicado: animal_histories.animal_file_id ahora acepta NULL.');

        return self::SUCCESS;
    }

    private function animalFileIdIsNullable(): bool
    {
        $row = DB::connection('rescate')->selectOne("
            SELECT is_nullable
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND table_name = 'animal_histories'
              AND column_name = 'animal_file_id'
            LIMIT 1
        ");

        return $row !== null && strtoupper((string) $row->is_nullable) === 'YES';
    }
}
