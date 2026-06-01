<?php

namespace App\Http\Controllers\Api\Fidele;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Fidele\StoreDemandeMesseRequest;
use App\Http\Resources\Api\Fidele\DemandeMesseResource;
use App\Models\DemandeMesse;
use App\Models\Fidele;
use App\Services\Fidele\DemandeMesseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DemandeMesseController extends Controller
{
    public function store(StoreDemandeMesseRequest $request, DemandeMesseService $service): JsonResponse
    {
        /** @var Fidele|null $fidele */
        $fidele = auth('fidele')->user();

        $demande = $service->creer($request->validated(), $fidele);

        return response()->json([
            'message' => 'Demande enregistrée. Procédez au paiement pour confirmation.',
            'demande' => new DemandeMesseResource($demande),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        /** @var Fidele $fidele */
        $fidele = $request->user();

        $demandes = $fidele->demandes()
            ->with(['paroisse', 'messe', 'typeOffrande', 'paiements'])
            ->latest()
            ->get();

        return response()->json([
            'demandes' => DemandeMesseResource::collection($demandes),
        ]);
    }

    public function show(Request $request, DemandeMesse $demandeMesse): JsonResponse
    {
        /** @var Fidele $fidele */
        $fidele = $request->user();

        abort_unless($demandeMesse->fidele_id === $fidele->id, 404);

        $demandeMesse->load(['paroisse', 'messe', 'typeOffrande', 'paiements']);

        return response()->json([
            'demande' => new DemandeMesseResource($demandeMesse),
        ]);
    }

    public function showByReference(string $reference): JsonResponse
    {
        $demande = DemandeMesse::query()
            ->where('reference', $reference)
            ->with(['paroisse', 'messe', 'typeOffrande', 'paiements'])
            ->firstOrFail();

        return response()->json([
            'demande' => new DemandeMesseResource($demande),
        ]);
    }
}
