<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }

    public function up(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        // Esquema unificado PG usa *_fkey; migraciones Laravel usan *_foreign.
        DB::statement('ALTER TABLE reports DROP CONSTRAINT IF EXISTS reports_persona_id_foreign');
        DB::statement('ALTER TABLE reports DROP CONSTRAINT IF EXISTS reports_persona_id_fkey');

        DB::statement('ALTER TABLE ONLY reports ALTER COLUMN persona_id DROP NOT NULL');

        if (! $this->foreignKeyExists('reports_persona_id_fkey')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreign('persona_id')
                    ->references('id')
                    ->on('people')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        DB::statement('ALTER TABLE reports DROP CONSTRAINT IF EXISTS reports_persona_id_foreign');
        DB::statement('ALTER TABLE reports DROP CONSTRAINT IF EXISTS reports_persona_id_fkey');

        DB::statement('DELETE FROM reports WHERE persona_id IS NULL');

        DB::statement('ALTER TABLE ONLY reports ALTER COLUMN persona_id SET NOT NULL');

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('persona_id')
                ->references('id')
                ->on('people')
                ->onDelete('cascade');
        });
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        $row = DB::selectOne(
            'SELECT 1 FROM pg_constraint WHERE conname = ?',
            [$constraintName]
        );

        return $row !== null;
    }
};
