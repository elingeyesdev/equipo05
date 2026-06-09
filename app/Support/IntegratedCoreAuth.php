<?php

namespace App\Support;

use App\Models\Usuario;

trait IntegratedCoreAuth
{
    protected function usesIntegratedCoreAuth(): bool
    {
        $model = config('auth.providers.users.model');

        return is_string($model) && $model === Usuario::class;
    }
}
