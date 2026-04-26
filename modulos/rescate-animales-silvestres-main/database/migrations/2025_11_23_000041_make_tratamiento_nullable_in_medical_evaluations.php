<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        // Postgres: quitar NOT NULL
        if (!$this->isSqlite() && Schema::hasTable('medical_evaluations')) {
            DB::statement('ALTER TABLE medical_evaluations ALTER COLUMN tratamiento_id DROP NOT NULL');
        }
    }

    public function down(): void
    {
        if (!$this->isSqlite() && Schema::hasTable('medical_evaluations')) {
            DB::statement('ALTER TABLE medical_evaluations ALTER COLUMN tratamiento_id SET NOT NULL');
        }
    }
};







