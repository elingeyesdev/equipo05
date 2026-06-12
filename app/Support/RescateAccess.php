<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Modules\Rescate\Models\Person;
use Modules\Rescate\Models\Report;

class RescateAccess
{
    public static function personIdForUser(?Usuario $user): ?int
    {
        if (! $user) {
            return null;
        }

        return Person::query()
            ->where('usuario_id', $user->getKey())
            ->value('id');
    }

    /** @return Builder<Report> */
    public static function scopeReportsQuery(Builder $query, ?Usuario $user): Builder
    {
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if (AccessControl::userHasAnyRole($user, [
            'Administrador', 'Rescatista', 'Veterinario', 'Cuidador',
        ])) {
            return $query;
        }

        if (AccessControl::userHasRole($user, 'Ciudadano')) {
            $personId = self::personIdForUser($user);

            return $personId
                ? $query->where('persona_id', $personId)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    public static function assertCanViewReport(Report $report): void
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        if (AccessControl::userHasAnyRole($user, [
            'Administrador', 'Rescatista', 'Veterinario', 'Cuidador',
        ])) {
            return;
        }

        if (AccessControl::userHasRole($user, 'Ciudadano')) {
            $personId = self::personIdForUser($user);
            abort_unless($personId && (int) $report->persona_id === (int) $personId, 403);

            return;
        }

        abort(403);
    }

    public static function assertCanManageReports(): void
    {
        abort_unless(
            AccessControl::userHasAnyRole(auth()->user(), ['Administrador', 'Rescatista', 'Veterinario']),
            403
        );
    }

    public static function assertCanApproveReports(): void
    {
        abort_unless(
            AccessControl::userHasAnyRole(auth()->user(), ['Administrador', 'Rescatista', 'Veterinario']),
            403
        );
    }

    public static function assertCanDeleteReports(): void
    {
        abort_unless(AccessControl::userHasRole(auth()->user(), 'Administrador'), 403);
    }

    public static function assertCanManagePeople(): void
    {
        abort_unless(AccessControl::userHasRole(auth()->user(), 'Administrador'), 403);
    }
}
