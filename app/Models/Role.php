<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    public function getConnectionName(): ?string
    {
        return \App\Support\UnifiedPostgres::enabled()
            ? \App\Support\UnifiedPostgres::coreAuthConnection()
            : parent::getConnectionName();
    }

    /**
     * Usuarios con este rol (siempre App\Models\Usuario en PG unificado).
     */
    public function users(): MorphToMany
    {
        $userModel = config('auth.providers.users.model', Usuario::class);

        return $this->morphedByMany(
            $userModel,
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
