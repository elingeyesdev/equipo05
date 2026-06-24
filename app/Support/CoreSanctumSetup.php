<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Garantiza que Sanctum pueda emitir tokens sobre core.usuarios en PostgreSQL unificado.
 */
class CoreSanctumSetup
{
    public static function ensurePersonalAccessTokensTable(): void
    {
        try {
            $connection = UnifiedPostgres::coreAuthConnection();

            if (UnifiedPostgres::enabled()) {
                DB::connection($connection)->statement(<<<'SQL'
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id              BIGSERIAL PRIMARY KEY,
    tokenable_type  VARCHAR(255) NOT NULL,
    tokenable_id    BIGINT NOT NULL,
    name            VARCHAR(255) NOT NULL,
    token           VARCHAR(64) NOT NULL UNIQUE,
    abilities       TEXT,
    last_used_at    TIMESTAMP(0) WITHOUT TIME ZONE,
    expires_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    created_at      TIMESTAMP(0) WITHOUT TIME ZONE,
    updated_at      TIMESTAMP(0) WITHOUT TIME ZONE
)
SQL);
                DB::connection($connection)->statement(
                    'CREATE INDEX IF NOT EXISTS core_pat_tokenable_idx ON personal_access_tokens (tokenable_type, tokenable_id)'
                );
                DB::connection($connection)->statement(
                    'CREATE INDEX IF NOT EXISTS core_pat_expires_at_idx ON personal_access_tokens (expires_at)'
                );

                return;
            }

            if (Schema::connection($connection)->hasTable('personal_access_tokens')) {
                return;
            }

            Schema::connection($connection)->create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        } catch (\Throwable $e) {
            Log::error('No se pudo preparar personal_access_tokens: '.$e->getMessage());
        }
    }
}
