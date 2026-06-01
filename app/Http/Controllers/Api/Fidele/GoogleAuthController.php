<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Fidele\GoogleAuthRequest;
use App\Http\Resources\Api\Fidele\FideleResource;
use App\Services\Auth\FideleGoogleAuthService;
use Illuminate\Http\JsonResponse;

class GoogleAuthController extends Controller
{
    public function __construct(
        private readonly FideleGoogleAuthService $googleAuth,
    ) {}

    public function __invoke(GoogleAuthRequest $request): JsonResponse
    {
        $result = $this->googleAuth->authenticate($request->validated('id_token'));

        return response()->json([
            'message' => 'Connexion Google réussie.',
            'user' => new FideleResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }
}
