<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Fidele\StoreCampagnePaiementRequest;
use App\Http\Requests\Api\Fidele\StorePaiementRequest;
use App\Http\Resources\Api\Fidele\PaiementResource;
use App\Models\CampagneCollecte;
use App\Models\DemandeMesse;
use App\Models\Fidele;
use App\Models\Paiement;
use App\Services\Fidele\PaiementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        /** @var Fidele $fidele */
        $fidele = $request->user();

        $paiements = Paiement::query()
            ->whereHas('demandeMesse', fn ($q) => $q->where('fidele_id', $fidele->id))
            ->with('moyenPaiement')
            ->latest()
            ->get();

        return response()->json([
            'paiements' => PaiementResource::collection($paiements),
        ]);
    }

    public function storeForDemande(
        StorePaiementRequest $request,
        DemandeMesse $demandeMesse,
        PaiementService $service,
    ): JsonResponse {
        abort_unless($demandeMesse->statut === 'en_attente', 422, 'Cette demande ne peut plus être payée.');

        $fidele = auth('fidele')->user();
        if ($fidele !== null) {
            abort_unless(
                $demandeMesse->fidele_id === null || $demandeMesse->fidele_id === $fidele->id,
                403
            );
        }

        $paiement = $service->initierPourDemande($demandeMesse, $request->validated());

        return response()->json([
            'message' => 'Paiement initié. En attente de confirmation du fournisseur.',
            'paiement' => new PaiementResource($paiement),
        ], 201);
    }

    public function storeForCampagne(
        StoreCampagnePaiementRequest $request,
        CampagneCollecte $campagneCollecte,
        PaiementService $service,
    ): JsonResponse {
        $paiement = $service->initierPourCampagne($campagneCollecte, $request->validated());

        return response()->json([
            'message' => 'Paiement initié pour la campagne.',
            'paiement' => new PaiementResource($paiement),
        ], 201);
    }

    public function confirmer(Paiement $paiement, PaiementService $service): JsonResponse
    {
        if (! app()->environment(['local', 'testing'])) {
            abort(403, 'Endpoint disponible uniquement en environnement de test.');
        }

        $paiement = $service->confirmer($paiement);

        return response()->json([
            'message' => 'Paiement confirmé.',
            'paiement' => new PaiementResource($paiement),
        ]);
    }
}
