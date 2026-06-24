<?php

namespace App\Models;

use App\Support\UnifiedPostgres;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    public function getConnectionName(): ?string
    {
        return UnifiedPostgres::coreAuthConnection();
    }
}
