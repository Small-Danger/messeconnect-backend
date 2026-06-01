<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Fidele\UpdateProfileRequest;
use App\Http\Resources\Api\Fidele\FideleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        /** @var \App\Models\Fidele $fidele */
        $fidele = $request->user();
        $fidele->update($request->validated());

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user' => new FideleResource($fidele->fresh()),
        ]);
    }
}
