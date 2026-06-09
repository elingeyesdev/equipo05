<?php

namespace App\Console\Commands;

use App\Models\Usuario;
use App\Services\Auth\CoreUserProvisioningService;
use App\Support\UnifiedPostgres;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPlatformUsersCommand extends Command
{
    protected $signature = 'auth:sync-platform-users
                            {--audit : Solo muestra diagnóstico sin escribir}';

    protected $description = 'Sincroniza usuarios de módulos hacia core.usuarios para login unificado';

    public function handle(CoreUserProvisioningService $provisioning): int
    {
        if (! UnifiedPostgres::enabled()) {
            $this->warn('DATABASE_UNIFIED_POSTGRES=false: el login central usa SQLite/local sin sync inventario→core.');

            return self::SUCCESS;
        }

        $this->audit($provisioning);

        if ($this->option('audit')) {
            return self::SUCCESS;
        }

        $stats = $provisioning->syncAllInventarioUsers();

        $this->newLine();
        $this->info('Sincronización inventario → core completada.');
        $this->line("  Sincronizados: {$stats['synced']}");
        $this->line("  Omitidos: {$stats['skipped']}");
        $this->line("  Errores: {$stats['errors']}");

        $this->audit($provisioning);

        return $stats['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function audit(CoreUserProvisioningService $provisioning): void
    {
        $core = Usuario::query()->get(['usuarioid', 'email', 'activo', 'contrasena']);
        $coreEmails = $core->mapWithKeys(fn ($u) => [$provisioning->normalizeEmail($u->email) => $u]);

        $this->info('=== Diagnóstico auth (core.usuarios) ===');
        $this->line('Usuarios en core: '.$core->count());

        $badHash = $core->filter(fn ($u) => ! $provisioning->isBcryptHash($u->contrasena));
        $this->line('Hashes inválidos: '.$badHash->count());

        $inactive = $core->where('activo', false);
        $this->line('Inactivos: '.$inactive->count());

        if (! DB::connection('inventario')->getSchemaBuilder()->hasTable('usuarios')) {
            $this->warn('Tabla inventario.usuarios no disponible.');

            return;
        }

        $inv = DB::connection('inventario')->table('usuarios')->select('id_usuario', 'correo', 'estado')->get();
        $missing = $inv->filter(function ($row) use ($coreEmails, $provisioning) {
            $email = $provisioning->normalizeEmail($row->correo);

            return $email !== '' && ! $coreEmails->has($email);
        });

        $this->line('Usuarios inventario sin cuenta core: '.$missing->count());
        foreach ($missing->take(10) as $row) {
            $this->line("  - {$row->correo} (id inventario {$row->id_usuario})");
        }
    }
}
