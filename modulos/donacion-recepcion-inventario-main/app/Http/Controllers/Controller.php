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
        abort_unless(auth()->user()?->canManage($permission), 403);
    }

    protected function assertAnyPermission(string ...$permissions): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->hasRole('Administrador')) {
            return;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return;
            }
        }

        abort(403);
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
