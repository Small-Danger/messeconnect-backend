<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\UpdateProfileRequest;
use App\Http\Resources\Api\Paroisse\ParoisseResource;
use App\Models\ConfigurationParoisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ResolvesParoisse;

    public function show(Request $request): JsonResponse
    {
        $paroisse = $this->paroisse($request)->load('diocese')->enrichProfilPublic();

        return response()->json([
            'paroisse' => new ParoisseResource($paroisse),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $paroisse = $this->paroisse($request);
        $data = $request->validated();
        $horaires = $data['horaires'] ?? null;
        unset($data['horaires']);

        if ($data !== []) {
            $paroisse->update($data);
        }

        if ($horaires !== null) {
            ConfigurationParoisse::query()->updateOrCreate(
                [
                    'paroisse_id' => $paroisse->id,
                    'cle' => 'horaires_secretariat',
                ],
                [
                    'valeur' => json_encode(array_values(array_filter(
                        $horaires,
                        fn ($line) => is_string($line) && trim($line) !== ''
                    ))),
                ]
            );
        }

        $paroisse->load('diocese')->enrichProfilPublic();

        return response()->json([
            'message' => 'Profil paroissial mis à jour.',
            'paroisse' => new ParoisseResource($paroisse),
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:logo,banniere'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $paroisse = $this->paroisse($request);
        $file = $request->file('image');

        if ($file === null) {
            abort(422, 'Image requise.');
        }

        $path = $file->store("paroisses/{$paroisse->id}/profil", 'public');
        $url = '/storage/'.str_replace('\\', '/', $path);
        $field = $request->string('type')->toString();

        $paroisse->update([$field => $url]);
        $paroisse->load('diocese')->enrichProfilPublic();

        return response()->json([
            'message' => 'Image de profil téléversée.',
            'url' => $url,
            'paroisse' => new ParoisseResource($paroisse),
        ], 201);
    }
}
