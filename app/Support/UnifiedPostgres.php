<?php

namespace App\Support;

class UnifiedPostgres
{
    public static function enabled(): bool
    {
        return filter_var(env('DATABASE_UNIFIED_POSTGRES', false), FILTER_VALIDATE_BOOL);
    }

    public static function coreAuthConnection(): string
    {
        return self::enabled() ? 'core' : (string) config('database.default', 'sqlite');
    }

    public static function transparenciaConnection(): string
    {
        return self::enabled() ? 'transparencia' : (string) config('database.default', 'sqlite');
    }
}
