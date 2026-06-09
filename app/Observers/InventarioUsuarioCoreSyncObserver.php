<?php

namespace App\Observers;

use App\Services\Auth\CoreUserProvisioningService;
use Modules\Inventario\Models\Usuario as InventarioUsuario;

class InventarioUsuarioCoreSyncObserver
{
    public function updated(InventarioUsuario $usuario): void
    {
        app(CoreUserProvisioningService::class)->syncFromInventario($usuario->fresh());
    }
}
