<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Api\Concerns\AuthenticatesWithSanctum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\Fidele\FideleResource;
use App\Models\Fidele;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesWithSanctum;

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->attemptLogin(
            modelClass: Fidele::class,
            email: $request->validated('email'),
            password: $request->validated('password'),
            tokenName: 'fidele-api',
        );

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => new FideleResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new FideleResource($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }
}
