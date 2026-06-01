<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreCampagneCollecteRequest;
use App\Http\Requests\Api\Paroisse\UpdateCampagneCollecteRequest;
use App\Http\Resources\Api\Paroisse\CampagneCollecteResource;
use App\Http\Resources\Api\Paroisse\CampagneDonParoisseResource;
use App\Models\CampagneCollecte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampagneCollecteController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $campagnes = $this->paroisse($request)
            ->campagneCollectes()
            ->latest()
            ->get();

        return response()->json([
            'campagnes' => CampagneCollecteResource::collection($campagnes),
        ]);
    }

    public function store(StoreCampagneCollecteRequest $request): JsonResponse
    {
        $campagne = $this->paroisse($request)
            ->campagneCollectes()
            ->create($request->validated());

        return response()->json([
            'message' => 'Campagne de collecte créée.',
            'campagne' => new CampagneCollecteResource($campagne),
        ], 201);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $paroisse = $this->paroisse($request);
        $file = $request->file('image');

        if ($file === null) {
            abort(422, 'Image requise.');
        }

        $path = $file->store("paroisses/{$paroisse->id}/campagnes", 'public');

        return response()->json([
            'message' => 'Image téléversée.',
            'url' => '/storage/'.str_replace('\\', '/', $path),
        ], 201);
    }

    public function show(Request $request, CampagneCollecte $campagneCollecte): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $campagneCollecte);

        return response()->json([
            'campagne' => new CampagneCollecteResource($campagneCollecte),
        ]);
    }

    public function dons(Request $request, CampagneCollecte $campagneCollecte): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $campagneCollecte);

        $dons = $campagneCollecte->paiements()
            ->with('moyenPaiement')
            ->orderByRaw("CASE statut WHEN 'reussi' THEN 0 WHEN 'en_attente' THEN 1 ELSE 2 END")
            ->orderByDesc('date_paiement')
            ->orderByDesc('created_at')
            ->get();

        $confirmes = $dons->where('statut', 'reussi');

        return response()->json([
            'dons' => CampagneDonParoisseResource::collection($dons),
            'resume' => [
                'total_confirmes' => (float) $confirmes->sum('montant'),
                'nombre_confirmes' => $confirmes->count(),
                'nombre_en_attente' => $dons->where('statut', 'en_attente')->count(),
            ],
        ]);
    }

    public function update(UpdateCampagneCollecteRequest $request, CampagneCollecte $campagneCollecte): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $campagneCollecte);
        $campagneCollecte->update($request->validated());

        return response()->json([
            'message' => 'Campagne de collecte mise à jour.',
            'campagne' => new CampagneCollecteResource($campagneCollecte),
        ]);
    }

    public function destroy(Request $request, CampagneCollecte $campagneCollecte): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $campagneCollecte);
        $campagneCollecte->delete();

        return response()->json([
            'message' => 'Campagne de collecte supprimée.',
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, CampagneCollecte $campagneCollecte): void
    {
        abort_unless(
            $campagneCollecte->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
