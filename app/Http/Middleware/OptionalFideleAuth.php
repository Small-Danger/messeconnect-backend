<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class OptionalFideleAuth
{
    /**
     * Authentifie le fidèle si un bearer token valide est présent, sans bloquer sinon.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token !== null) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken !== null && $accessToken->tokenable instanceof \App\Models\Fidele) {
                auth('fidele')->setUser($accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
