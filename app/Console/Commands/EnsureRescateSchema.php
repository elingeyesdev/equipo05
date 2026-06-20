<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureRescateSchema extends Command
{
    protected $signature = 'rescate:ensure-schema';

    protected $description = 'Aplica parches de esquema rescate en PostgreSQL unificado (idempotente)';

    public function handle(): int
    {
        if (DB::connection('rescate')->getDriverName() !== 'pgsql') {
            return self::SUCCESS;
        }

        if (! Schema::connection('rescate')->hasTable('transfers')
            && ! Schema::connection('rescate')->hasTable('animal_histories')) {
            $this->components->warn('Esquema rescate aun no inicializado; omitiendo parches.');

            return self::SUCCESS;
        }

        $this->applySqlPatch('03b_mod_rescate_transfers_persona.sql', 'transfers persona_id');
        $this->applyAnimalHistoriesNullable();

        $this->components->info('Esquema rescate verificado.');

        return self::SUCCESS;
    }

    private function applySqlPatch(string $filename, string $label): void
    {
        $path = database_path('unified_postgresql/'.$filename);
        if (! is_file($path)) {
            $this->components->error("Falta {$filename}");

            return;
        }

        if ($filename === '03b_mod_rescate_transfers_persona.sql' && ! $this->transfersNeedPersonaPatch()) {
            $this->components->info("Parche {$label}: ya aplicado.");

            return;
        }

        DB::connection('rescate')->unprepared((string) file_get_contents($path));
        $this->components->info("Parche aplicado: {$label}.");
    }

    private function applyAnimalHistoriesNullable(): void
    {
        if (! Schema::connection('rescate')->hasTable('animal_histories')) {
            return;
        }

        if ($this->animalFileIdIsNullable()) {
            $this->components->info('Parche animal_histories: ya nullable.');

            return;
        }

        $path = database_path('unified_postgresql/03c_animal_histories_nullable.sql');
        if (! is_file($path)) {
            $this->components->error('Falta 03c_animal_histories_nullable.sql');

            return;
        }

        DB::connection('rescate')->unprepared((string) file_get_contents($path));
        $this->components->info('Parche aplicado: animal_histories.animal_file_id nullable.');
    }

    private function transfersNeedPersonaPatch(): bool
    {
        if (! Schema::connection('rescate')->hasTable('transfers')) {
            return false;
        }

        return Schema::connection('rescate')->hasColumn('transfers', 'rescatista_id');
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
