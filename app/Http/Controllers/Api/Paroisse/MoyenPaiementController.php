<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreMoyenPaiementRequest;
use App\Http\Requests\Api\Paroisse\UpdateMoyenPaiementRequest;
use App\Http\Resources\Api\Paroisse\MoyenPaiementResource;
use App\Models\MoyenPaiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MoyenPaiementController extends Controller
{
    use ResolvesParoisse;

    public function index(Request $request): JsonResponse
    {
        $moyens = $this->paroisse($request)
            ->moyenPaiements()
            ->latest()
            ->get();

        return response()->json([
            'moyen_paiements' => MoyenPaiementResource::collection($moyens),
        ]);
    }

    public function store(StoreMoyenPaiementRequest $request): JsonResponse
    {
        $moyen = $this->paroisse($request)
            ->moyenPaiements()
            ->create($request->validated());

        return response()->json([
            'message' => 'Moyen de paiement ajouté.',
            'moyen_paiement' => new MoyenPaiementResource($moyen),
        ], 201);
    }

    public function show(Request $request, MoyenPaiement $moyenPaiement): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $moyenPaiement);

        return response()->json([
            'moyen_paiement' => new MoyenPaiementResource($moyenPaiement),
        ]);
    }

    public function update(UpdateMoyenPaiementRequest $request, MoyenPaiement $moyenPaiement): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $moyenPaiement);
        $moyenPaiement->update($request->validated());

        return response()->json([
            'message' => 'Moyen de paiement mis à jour.',
            'moyen_paiement' => new MoyenPaiementResource($moyenPaiement),
        ]);
    }

    public function destroy(Request $request, MoyenPaiement $moyenPaiement): JsonResponse
    {
        $this->ensureBelongsToParoisse($request, $moyenPaiement);
        $moyenPaiement->delete();

        return response()->json([
            'message' => 'Moyen de paiement supprimé.',
        ]);
    }

    private function ensureBelongsToParoisse(Request $request, MoyenPaiement $moyenPaiement): void
    {
        abort_unless(
            $moyenPaiement->paroisse_id === $this->paroisse($request)->id,
            404
        );
    }
}
