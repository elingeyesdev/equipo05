<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class UnifiedValidation
{
    public static function coreUsuariosTable(): string
    {
        return UnifiedPostgres::enabled() ? 'core.usuarios' : 'usuarios';
    }

    public static function coreUsuariosKey(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarioid' : 'id';
    }

    public static function incendiosUsersTable(): string
    {
        return UnifiedPostgres::enabled() ? 'core.usuarios' : 'users';
    }

    public static function incendiosUsersKey(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarioid' : 'id';
    }

    public static function incendiosTable(string $table): string
    {
        return UnifiedPostgres::enabled() ? "incendios.{$table}" : $table;
    }

    public static function transparenciaTable(string $table): string
    {
        return UnifiedPostgres::enabled() ? "transparencia.{$table}" : $table;
    }

    public static function existsTransparencia(string $table, string $column): string
    {
        return 'exists:'.self::transparenciaTable($table).','.$column;
    }

    public static function existsCoreUsuario(string $column = 'usuarioid'): string
    {
        return 'exists:'.self::coreUsuariosTable().','.$column;
    }

    public static function uniqueTransparencia(string $table, string $column, ?int $ignoreId = null, ?string $ignoreKey = null): string
    {
        $rule = 'unique:'.self::transparenciaTable($table).','.$column;
        if ($ignoreId !== null) {
            $rule .= ','.$ignoreId.','.($ignoreKey ?? 'id');
        }

        return $rule;
    }

    public static function uniqueCoreEmail(?int $ignoreUserId = null): array
    {
        $rule = Rule::unique(self::coreUsuariosTable(), 'email');

        if ($ignoreUserId !== null) {
            $rule->ignore($ignoreUserId, self::coreUsuariosKey());
        }

        return ['required', 'email', 'max:255', $rule];
    }

    /** @return array<int, mixed> */
    public static function splitNombreCompleto(string $nombreCompleto): array
    {
        $parts = preg_split('/\s+/', trim($nombreCompleto), 2, PREG_SPLIT_NO_EMPTY);

        return [
            'nombre' => $parts[0] ?? 'Usuario',
            'apellido' => $parts[1] ?? '-',
        ];
    }
}
