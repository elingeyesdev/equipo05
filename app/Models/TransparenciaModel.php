<?php

namespace App\Models;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelos del dominio transparencia/donaciones (campanias, donaciones, mensajes, etc.).
 * En PostgreSQL unificado usan el esquema transparencia; usuarios siguen en core.
 */
abstract class TransparenciaModel extends Model
{
    public function getConnectionName(): ?string
    {
        if (UnifiedPostgres::enabled()) {
            return 'transparencia';
        }

        return parent::getConnectionName();
    }
}
