<?php

namespace App\Http\Middleware;

use App\Support\MobileTokenService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobile
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = MobileTokenService::resolveUser($request->bearerToken());

        if ($user === null) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        auth()->setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
