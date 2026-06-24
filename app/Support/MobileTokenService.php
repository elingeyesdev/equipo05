<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class MobileTokenService
{
    private const CACHE_PREFIX = 'alas_mobile_token:';

    public static function issue(Usuario $user): string
    {
        CoreSanctumSetup::ensurePersonalAccessTokensTable();

        try {
            $user->tokens()->delete();

            return $user->createToken('alas-mobile')->plainTextToken;
        } catch (\Throwable) {
            return self::issueCacheToken($user);
        }
    }

    public static function resolveUser(?string $bearer): ?Usuario
    {
        if ($bearer === null || $bearer === '') {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($bearer);
        if ($accessToken !== null) {
            $user = $accessToken->tokenable;

            return $user instanceof Usuario ? $user : null;
        }

        $userId = Cache::get(self::CACHE_PREFIX.hash('sha256', $bearer));
        if ($userId === null) {
            return null;
        }

        return Usuario::query()->find($userId);
    }

    public static function revoke(?string $bearer): void
    {
        if ($bearer === null || $bearer === '') {
            return;
        }

        $accessToken = PersonalAccessToken::findToken($bearer);
        if ($accessToken !== null) {
            $accessToken->delete();

            return;
        }

        Cache::forget(self::CACHE_PREFIX.hash('sha256', $bearer));
    }

    private static function issueCacheToken(Usuario $user): string
    {
        $plain = Str::random(64);
        Cache::put(
            self::CACHE_PREFIX.hash('sha256', $plain),
            $user->getKey(),
            now()->addDays(30),
        );

        return $plain;
    }
}
