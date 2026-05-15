<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

final class YearMonthSql
{
    public static function yearMonthSelect(string $column, ?string $connection = null): string
    {
        if (self::driver($connection) === 'pgsql') {
            return "EXTRACT(YEAR FROM {$column})::integer as anio, EXTRACT(MONTH FROM {$column})::integer as mes";
        }

        return "CAST(strftime('%Y', {$column}) AS INTEGER) as anio, CAST(strftime('%m', {$column}) AS INTEGER) as mes";
    }

    public static function yearMonthGroupByRaw(string $column, ?string $connection = null): string
    {
        if (self::driver($connection) === 'pgsql') {
            return "EXTRACT(YEAR FROM {$column}), EXTRACT(MONTH FROM {$column})";
        }

        return 'anio, mes';
    }

    public static function monthSelect(string $column, ?string $connection = null): string
    {
        if (self::driver($connection) === 'pgsql') {
            return "EXTRACT(MONTH FROM {$column})::integer as mes";
        }

        return "CAST(strftime('%m', {$column}) AS INTEGER) as mes";
    }

    public static function monthGroupByRaw(string $column, ?string $connection = null): string
    {
        if (self::driver($connection) === 'pgsql') {
            return "EXTRACT(MONTH FROM {$column})";
        }

        return "CAST(strftime('%m', {$column}) AS INTEGER)";
    }

    private static function driver(?string $connection): string
    {
        return $connection !== null
            ? DB::connection($connection)->getDriverName()
            : DB::connection()->getDriverName();
    }
}
