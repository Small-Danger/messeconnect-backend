<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreMediaParoisseRequest;
use App\Http\Requests\Api\Paroisse\UpdateMediaParoisseRequest;
use App\Http\Resources\Api\Paroisse\MediaParoisseResource;
use App\Models\MediaParoisse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaParoisseController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $medias = $this->paroisse($request)
            ->medias()
            ->orderBy('ordre')
            ->get();

        return response()->json([
            'medias' => MediaParoisseResource::collection($medias),
        ]);
    }

    public function store(StoreMediaParoisseRequest $request): JsonResponse
    {
        $media = $this->paroisse($request)
            ->medias()
            ->create($request->validated());

        return response()->json([
            'message' => 'Média ajouté.',
            'media' => new MediaParoisseResource($media),
        ], 201);
    }

    public function show(Request $request, MediaParoisse $media): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $media);

        return response()->json([
            'media' => new MediaParoisseResource($media),
        ]);
    }

    public function update(UpdateMediaParoisseRequest $request, MediaParoisse $media): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $media);
        $media->update($request->validated());

        return response()->json([
            'message' => 'Média mis à jour.',
            'media' => new MediaParoisseResource($media),
        ]);
    }

    public function destroy(Request $request, MediaParoisse $media): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $media);
        $media->delete();

        return response()->json([
            'message' => 'Média supprimé.',
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, MediaParoisse $media): void
    {
        abort_unless(
            $media->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
