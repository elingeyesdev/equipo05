<?php

namespace Modules\Inventario\Http\Controllers;

use App\Support\AccessControl;
use App\Support\OwnershipScope;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function assertPermission(string $permission): void
    {
        abort_unless(AccessControl::userCan(auth()->user(), $permission), 403);
    }

    protected function assertAnyPermission(string ...$permissions): void
    {
        abort_unless(AccessControl::userCanAny(auth()->user(), $permissions), 403);
    }

    protected function assertAlmaceneroOrAdmin(): void
    {
        abort_unless(
            AccessControl::userHasAnyRole(auth()->user(), ['Administrador', 'Almacenero']),
            403
        );
    }

    protected function assertNotDonanteOnly(): void
    {
        $user = auth()->user();
        abort_if($user && $user->hasRole('Donante') && ! $user->hasRole('Administrador'), 403);
    }
}
