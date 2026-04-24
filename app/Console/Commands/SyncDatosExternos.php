<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncDatosExternos extends Command
{
    /**
     * El nombre del comando (lo que ejecutarás)
     */
    protected $signature = 'externo:sync-datos';

    /**
     * Descripción del comando
     */
    protected $description = 'Sincroniza campañas, donaciones monetarias, donaciones en especie y almacenes desde la API externa.';

    /**
     * Ejecuta el comando
     */
    public function handle()
    {
        $this->info('=== Iniciando sincronización de datos externos ===');

        // 1. Campañas
        try {
            app(\App\Http\Controllers\ApiCampaniaSyncController::class)->sync();
            $this->info('✔ Campañas sincronizadas.');
        } catch (\Exception $e) {
            $this->error('✘ Error al sincronizar campañas: ' . $e->getMessage());
        }

        // 2. Donaciones monetarias
        try {
            app(\App\Http\Controllers\ApiDonacionSyncController::class)->syncDinero();
            $this->info('✔ Donaciones monetarias sincronizadas.');
        } catch (\Exception $e) {
            $this->error('✘ Error al sincronizar donaciones monetarias: ' . $e->getMessage());
        }

        // 3. Donaciones en especie + trazabilidad
        try {
            app(\App\Http\Controllers\Ext\TrazabilidadSyncController::class)->syncEspecie();
            $this->info('✔ Trazabilidad especie sincronizada.');
        } catch (\Exception $e) {
            $this->error('✘ Error al sincronizar trazabilidad especie: ' . $e->getMessage());
        }

        // 4. Almacenes completos
        try {
            app(\App\Http\Controllers\Ext\IntegracionExternaController::class)->syncAlmacenes();
            $this->info('✔ Almacenes sincronizados.');
        } catch (\Exception $e) {
            $this->error('✘ Error al sincronizar almacenes: ' . $e->getMessage());
        }

        $this->info('=== Sincronización finalizada ===');
    }
}
