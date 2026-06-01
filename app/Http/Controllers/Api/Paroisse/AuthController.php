<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Concerns\AuthenticatesWithSanctum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Api\Paroisse\UserParoisseResource;
use App\Models\UserParoisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use AuthenticatesWithSanctum;

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->attemptLogin(
            modelClass: UserParoisse::class,
            email: $request->validated('email'),
            password: $request->validated('password'),
            tokenName: 'paroisse-api',
            trackLastLogin: true,
        );

        $result['user']->load('paroisse');

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => new UserParoisseResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $request->user()->load('paroisse');

        return response()->json([
            'user' => new UserParoisseResource($request->user()),
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
