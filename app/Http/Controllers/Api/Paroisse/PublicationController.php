<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StorePublicationRequest;
use App\Http\Requests\Api\Paroisse\UpdatePublicationRequest;
use App\Http\Resources\Api\Paroisse\PublicationResource;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $publications = $this->paroisse($request)
            ->publications()
            ->latest('date_publication')
            ->get();

        return response()->json([
            'publications' => PublicationResource::collection($publications),
        ]);
    }

    public function store(StorePublicationRequest $request): JsonResponse
    {
        $data = $this->preparePublicationData($request->validated());
        $data['date_publication'] ??= now();

        $publication = $this->paroisse($request)->publications()->create($data);

        return response()->json([
            'message' => 'Publication créée.',
            'publication' => new PublicationResource($publication),
        ], 201);
    }

    public function show(Request $request, Publication $publication): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $publication);

        return response()->json([
            'publication' => new PublicationResource($publication),
        ]);
    }

    public function update(UpdatePublicationRequest $request, Publication $publication): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $publication);
        $publication->update($this->preparePublicationData($request->validated()));

        return response()->json([
            'message' => 'Publication mise à jour.',
            'publication' => new PublicationResource($publication),
        ]);
    }

    public function uploadImages(Request $request): JsonResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['image', 'max:5120'],
        ]);

        $paroisse = $this->paroisse($request);
        $urls = [];

        foreach ($request->file('images', []) as $file) {
            if ($file === null) {
                continue;
            }

            $path = $file->store("paroisses/{$paroisse->id}/publications", 'public');
            $urls[] = '/storage/'.str_replace('\\', '/', $path);
        }

        return response()->json([
            'message' => 'Images téléversées.',
            'urls' => $urls,
        ], 201);
    }

    public function destroy(Request $request, Publication $publication): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $publication);
        $publication->delete();

        return response()->json([
            'message' => 'Publication supprimée.',
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, Publication $publication): void
    {
        abort_unless(
            $publication->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function preparePublicationData(array $data): array
    {
        if (array_key_exists('images', $data)) {
            $images = array_values(array_filter($data['images'] ?? []));
            $data['images'] = $images !== [] ? $images : null;
            $data['image'] = $images[0] ?? null;
        }

        return $data;
    }
}
