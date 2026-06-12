<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\Donante;

class OwnershipScope
{
    public static function inventarioDonanteId(?Usuario $user): ?int
    {
        if (! $user || ! $user->hasRole('Donante')) {
            return null;
        }

        $email = strtolower(trim((string) $user->email));
        if ($email === '') {
            return null;
        }

        return Donante::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->value('id_donante');
    }

    public static function ensureInventarioDonanteProfile(?Usuario $user): Donante
    {
        abort_unless($user && $user->hasRole('Donante'), 403);

        $email = strtolower(trim((string) $user->email));
        $nombre = trim((string) $user->nombre.' '.(string) $user->apellido);

        $donante = Donante::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $donante) {
            $donante = Donante::create([
                'nombre' => $nombre !== '' ? $nombre : $email,
                'tipo' => 'Persona',
                'email' => $email,
                'telefono' => $user->telefono,
                'fecha_registro' => now(),
            ]);
        }

        return $donante;
    }

    /** @return Builder<Donacione> */
    public static function scopedDonacionesQuery(?Usuario $user): Builder
    {
        $query = Donacione::query();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('Administrador') || $user->hasRole('Almacenero')) {
            return $query;
        }

        if ($user->hasRole('Donante')) {
            $donanteId = self::inventarioDonanteId($user);

            return $donanteId
                ? $query->where('id_donante', $donanteId)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    public static function assertCanAccessDonacion(?Usuario $user, Donacione $donacion): void
    {
        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('Administrador') || $user->hasRole('Almacenero')) {
            return;
        }

        if ($user->hasRole('Donante')) {
            $donanteId = self::inventarioDonanteId($user);
            abort_unless($donanteId && (int) $donacion->id_donante === (int) $donanteId, 403);

            return;
        }

        abort(403);
    }

    public static function assertCanMutateDonacion(?Usuario $user, ?Donacione $donacion = null): void
    {
        if (! $user) {
            abort(403);
        }

        if ($user->hasRole('Administrador') || $user->hasRole('Almacenero')) {
            return;
        }

        if ($user->hasRole('Donante')) {
            if ($donacion) {
                self::assertCanAccessDonacion($user, $donacion);
            }

            return;
        }

        abort(403);
    }
}
