<?php

namespace App\Console\Commands;

use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ConsolidateUsersToCoreCommand extends Command
{
    protected $signature = 'db:consolidate-users-core
                            {--fresh-core : Ejecuta 00_core_auth.sql antes de migrar datos}';

    protected $description = 'Unifica usuarios de transparencia/incendios/rescate en core.usuarios (PostgreSQL unificado)';

    public function handle(): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->error('Activa DATABASE_UNIFIED_POSTGRES=true en .env');

            return self::FAILURE;
        }

        $core = DB::connection('core');

        if ($core->getDriverName() !== 'pgsql') {
            $this->error('La conexión core debe ser PostgreSQL (revisa DATABASE_UNIFIED_POSTGRES y UNIFIED_PG_*).');

            return self::FAILURE;
        }

        if ($this->option('fresh-core')) {
            $path = database_path('unified_postgresql/00_core_auth.sql');
            if (! File::isFile($path)) {
                $this->error('No se encontró 00_core_auth.sql');

                return self::FAILURE;
            }
            $core->unprepared(File::get($path));
            $this->info('Esquema core.usuarios aplicado.');
        } elseif (! Schema::connection('core')->hasTable('usuarios')) {
            $this->error('Falta core.usuarios. Ejecuta: php artisan db:consolidate-users-core --fresh-core');

            return self::FAILURE;
        }

        $migrated = 0;
        $migrated += $this->copyFromSchema('transparencia', 'usuarios', [
            'usuarioid' => 'usuarioid',
            'email' => 'email',
            'contrasena' => 'contrasena',
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'telefono' => 'telefono',
            'imagenurl' => 'imagenurl',
            'activo' => 'activo',
            'fecharegistro' => 'fecharegistro',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ]);
        $migrated += $this->copyIncendiosUsers();
        $migrated += $this->copyRescateUsers();
        $this->copySpatieFromSchema('transparencia');
        $this->copySpatieFromSchema('rescate');
        $this->rewireForeignKeys();
        $this->dropShadowUserTables();
        $this->resetCoreSequences();

        $this->info("Usuarios consolidados en core.usuarios ({$migrated} filas nuevas/actualizadas).");
        $this->line('Ejecuta: php artisan db:seed --class=Database\\Seeders\\RoleSeeder');
        $this->line('       php artisan db:seed --class=Database\\Seeders\\UserSeeder');

        return self::SUCCESS;
    }

    private function copyFromSchema(string $schema, string $table, array $columnMap): int
    {
        if (! $this->tableExists($schema, $table)) {
            return 0;
        }

        $rows = DB::connection('core')->select("SELECT * FROM {$schema}.{$table}");
        $count = 0;
        foreach ($rows as $row) {
            $payload = [];
            foreach ($columnMap as $target => $source) {
                $payload[$target] = $row->{$source} ?? null;
            }
            if (empty($payload['email'])) {
                continue;
            }
            $exists = DB::connection('core')->table('usuarios')->where('email', $payload['email'])->exists();
            if ($exists) {
                DB::connection('core')->table('usuarios')->where('email', $payload['email'])->update($payload);
            } else {
                if (! empty($payload['usuarioid'])) {
                    DB::connection('core')->table('usuarios')->insert($payload);
                } else {
                    DB::connection('core')->table('usuarios')->insert($payload);
                }
            }
            $count++;
        }

        return $count;
    }

    private function copyIncendiosUsers(): int
    {
        if (! $this->tableExists('incendios', 'users')) {
            return 0;
        }

        $rows = DB::connection('core')->select('SELECT * FROM incendios.users');
        $count = 0;
        foreach ($rows as $row) {
            $parts = preg_split('/\s+/', trim((string) $row->name), 2);
            $nombre = $parts[0] ?: 'Usuario';
            $apellido = $parts[1] ?? (string) $row->id;
            $payload = [
                'usuarioid' => $row->id,
                'email' => $row->email,
                'contrasena' => $row->password,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'telefono' => $row->telefono,
                'activo' => true,
                'fecharegistro' => $row->created_at ?? now(),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];
            $this->upsertCoreUser($payload);
            $count++;
        }

        return $count;
    }

    private function copyRescateUsers(): int
    {
        if (! $this->tableExists('rescate', 'users')) {
            return 0;
        }

        $rows = DB::connection('core')->select('SELECT * FROM rescate.users');
        $count = 0;
        foreach ($rows as $row) {
            $local = explode('@', (string) $row->email)[0] ?: 'Usuario';
            $payload = [
                'usuarioid' => $row->id,
                'email' => $row->email,
                'contrasena' => $row->password,
                'nombre' => $local,
                'apellido' => 'Rescate',
                'activo' => true,
                'fecharegistro' => $row->created_at ?? now(),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];
            $this->upsertCoreUser($payload);
            $count++;
        }

        return $count;
    }

    private function upsertCoreUser(array $payload): void
    {
        $core = DB::connection('core')->table('usuarios');
        $byId = ! empty($payload['usuarioid'])
            ? $core->where('usuarioid', $payload['usuarioid'])->exists()
            : false;
        $byEmail = $core->where('email', $payload['email'])->exists();

        if ($byId) {
            $core->where('usuarioid', $payload['usuarioid'])->update($payload);
        } elseif ($byEmail) {
            unset($payload['usuarioid']);
            $core->where('email', $payload['email'])->update($payload);
        } else {
            $core->insert($payload);
        }
    }

    private function copySpatieFromSchema(string $schema): void
    {
        foreach (['permissions', 'roles', 'role_has_permissions', 'model_has_roles', 'model_has_permissions'] as $table) {
            if (! $this->tableExists($schema, $table) || ! $this->tableExists('core', $table)) {
                continue;
            }
            $rows = DB::connection('core')->select("SELECT * FROM {$schema}.{$table}");
            foreach ($rows as $row) {
                $data = (array) $row;
                try {
                    DB::connection('core')->table($table)->insertOrIgnore($data);
                } catch (\Throwable) {
                    // filas ya existentes en core
                }
            }
        }
    }

    private function rewireForeignKeys(): void
    {
        $statements = [
            "ALTER TABLE IF EXISTS incendios.administradores DROP CONSTRAINT IF EXISTS administradores_user_id_foreign",
            "ALTER TABLE IF EXISTS incendios.voluntarios DROP CONSTRAINT IF EXISTS voluntarios_user_id_foreign",
            "ALTER TABLE IF EXISTS rescate.people DROP CONSTRAINT IF EXISTS people_usuario_id_foreign",
            "ALTER TABLE IF EXISTS incendios.administradores
                ADD CONSTRAINT administradores_user_id_foreign
                FOREIGN KEY (user_id) REFERENCES core.usuarios(usuarioid) ON DELETE CASCADE",
            "ALTER TABLE IF EXISTS incendios.voluntarios
                ADD CONSTRAINT voluntarios_user_id_foreign
                FOREIGN KEY (user_id) REFERENCES core.usuarios(usuarioid) ON DELETE CASCADE",
            "ALTER TABLE IF EXISTS rescate.people
                ADD CONSTRAINT people_usuario_id_foreign
                FOREIGN KEY (usuario_id) REFERENCES core.usuarios(usuarioid) ON DELETE CASCADE",
        ];

        foreach ($statements as $sql) {
            try {
                DB::connection('core')->statement($sql);
            } catch (\Throwable $e) {
                $this->warn($e->getMessage());
            }
        }
    }

    private function dropShadowUserTables(): void
    {
        foreach (['incendios.users', 'rescate.users', 'logistica.users'] as $qualified) {
            try {
                DB::connection('core')->statement("DROP TABLE IF EXISTS {$qualified} CASCADE");
                $this->line("Eliminada tabla sombra {$qualified}");
            } catch (\Throwable $e) {
                $this->warn("No se pudo eliminar {$qualified}: ".$e->getMessage());
            }
        }

        if ($this->tableExists('transparencia', 'usuarios')) {
            try {
                DB::connection('core')->statement('DROP TABLE IF EXISTS transparencia.usuarios CASCADE');
                $this->line('Eliminada transparencia.usuarios (ahora en core)');
            } catch (\Throwable $e) {
                $this->warn($e->getMessage());
            }
        }
    }

    private function resetCoreSequences(): void
    {
        foreach ([
            ['usuarios', 'usuarioid'],
            ['roles', 'id'],
            ['permissions', 'id'],
        ] as [$table, $column]) {
            try {
                DB::connection('core')->statement(
                    "SELECT setval(pg_get_serial_sequence('core.{$table}', '{$column}'), COALESCE((SELECT MAX({$column}) FROM core.{$table}), 1), true)"
                );
            } catch (\Throwable) {
                // tabla o secuencia inexistente
            }
        }
    }

    private function tableExists(string $schema, string $table): bool
    {
        if (DB::connection('core')->getDriverName() !== 'pgsql') {
            return false;
        }

        $row = DB::connection('core')->selectOne(
            'SELECT EXISTS (
                SELECT 1 FROM information_schema.tables
                WHERE table_schema = ? AND table_name = ?
            ) AS exists',
            [$schema, $table]
        );

        return (bool) ($row->exists ?? false);
    }
}
