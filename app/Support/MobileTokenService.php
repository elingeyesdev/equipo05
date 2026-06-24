<?php

namespace App\Support;

use App\Models\Usuario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
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
        } catch (\Throwable $e) {
            Log::warning('Sanctum no disponible, token firmado: '.$e->getMessage());
        }

        try {
            return self::issueCacheToken($user);
        } catch (\Throwable $e) {
            Log::warning('Cache token no disponible, token cifrado: '.$e->getMessage());
        }

        return self::issueEncryptedToken($user);
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
        if ($userId !== null) {
            return Usuario::query()->find($userId);
        }

        return self::resolveEncryptedUser($bearer);
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

    private static function issueEncryptedToken(Usuario $user): string
    {
        return Crypt::encryptString(json_encode([
            'uid' => (int) $user->getKey(),
            'exp' => now()->addDays(30)->timestamp,
        ], JSON_THROW_ON_ERROR));
    }

    private static function resolveEncryptedUser(string $token): ?Usuario
    {
        try {
            $payload = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($payload)) {
                return null;
            }
            $exp = (int) ($payload['exp'] ?? 0);
            if ($exp < time()) {
                return null;
            }

            return Usuario::query()->find($payload['uid'] ?? 0);
        } catch (\Throwable) {
            return null;
        }
    }
}
