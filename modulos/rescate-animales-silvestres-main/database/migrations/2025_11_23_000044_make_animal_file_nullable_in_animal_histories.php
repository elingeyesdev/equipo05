<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }

    public function up(): void
    {
        if (! $this->isSqlite() && Schema::connection('rescate')->hasTable('animal_histories')) {
            DB::connection('rescate')->statement('ALTER TABLE animal_histories ALTER COLUMN animal_file_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (! $this->isSqlite() && Schema::connection('rescate')->hasTable('animal_histories')) {
            DB::connection('rescate')->statement('ALTER TABLE animal_histories ALTER COLUMN animal_file_id SET NOT NULL');
        }
    }
};





