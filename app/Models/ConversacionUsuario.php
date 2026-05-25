<?php

namespace App\Models;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ConversacionUsuario extends Pivot
{
    protected $table = 'conversacion_usuarios';

    public $timestamps = false;

    public function getConnectionName(): ?string
    {
        return UnifiedPostgres::transparenciaConnection();
    }
}
