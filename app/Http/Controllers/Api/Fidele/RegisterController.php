<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Fidele\RegisterFideleRequest;
use App\Http\Resources\Api\Fidele\FideleResource;
use App\Models\Fidele;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function store(RegisterFideleRequest $request): JsonResponse
    {
        $fidele = Fidele::query()->create([
            ...$request->validated(),
            'actif' => true,
        ]);

        $token = $fidele->createToken('fidele-api')->plainTextToken;

        return response()->json([
            'message' => 'Compte fidèle créé avec succès.',
            'user' => new FideleResource($fidele),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
