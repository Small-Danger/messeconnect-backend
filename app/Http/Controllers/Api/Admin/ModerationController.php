<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Admin\Concerns\LogsAdminAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UpdatePublicationVisibleRequest;
use App\Http\Resources\Api\Admin\CampagneCollecteResource;
use App\Http\Resources\Api\Admin\PublicationResource;
use App\Models\CampagneCollecte;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    use LogsAdminAction;

    public function publications(Request $request): JsonResponse
    {
        $query = Publication::query()->with('paroisse')->latest('date_publication');

        if ($request->has('visible')) {
            $query->where('visible', $request->boolean('visible'));
        }

        $publications = $query->get();

        return response()->json([
            'publications' => PublicationResource::collection($publications),
        ]);
    }

    public function updatePublicationVisible(
        UpdatePublicationVisibleRequest $request,
        Publication $publication,
    ): JsonResponse {
        $publication->update(['visible' => $request->boolean('visible')]);

        $this->logAdminAction($request, 'publication.visible', [
            'publication_id' => $publication->id,
            'visible' => $publication->visible,
        ]);

        return response()->json([
            'message' => 'Visibilité de la publication mise à jour.',
            'publication' => new PublicationResource($publication->fresh()->load('paroisse')),
        ]);
    }

    public function campagnes(Request $request): JsonResponse
    {
        $query = CampagneCollecte::query()->with('paroisse')->latest();

        if ($request->boolean('actives')) {
            $query->where(function ($sub) {
                $sub->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', now()->toDateString());
            });
        }

        $campagnes = $query->get();

        return response()->json([
            'campagnes' => CampagneCollecteResource::collection($campagnes),
        ]);
    }
}
