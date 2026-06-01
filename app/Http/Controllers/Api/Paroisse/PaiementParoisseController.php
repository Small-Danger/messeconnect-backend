<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Paroisse\PaiementEnAttenteResource;
use App\Models\Paiement;
use App\Services\Fidele\PaiementService;
use App\Services\Paroisse\DemandeMesseStatutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaiementParoisseController extends Controller
{
    use ResolvesParoisse;

    public function indexEnAttente(Request $request): JsonResponse
    {
        $paroisseId = $this->paroisse($request)->id;

        $paiements = Paiement::query()
            ->where('statut', 'en_attente')
            ->whereHas('moyenPaiement', fn ($q) => $q
                ->where('paroisse_id', $paroisseId)
                ->where('type', 'autre'))
            ->whereHas('demandeMesse', fn ($q) => $q->where('paroisse_id', $paroisseId))
            ->with(['demandeMesse.messe', 'demandeMesse.typeOffrande', 'moyenPaiement'])
            ->latest()
            ->get();

        return response()->json([
            'paiements' => PaiementEnAttenteResource::collection($paiements),
        ]);
    }

    public function confirmer(
        Request $request,
        Paiement $paiement,
        PaiementService $paiementService,
    ): JsonResponse {
        $this->ensurePaiementBelongsToParoisse($request, $paiement);

        abort_unless($paiement->statut === 'en_attente', 422, 'Ce paiement ne peut plus être confirmé.');

        $paiement = $paiementService->confirmer($paiement);
        $paiement->load(['demandeMesse.messe', 'demandeMesse.typeOffrande']);

        return response()->json([
            'message' => 'Paiement confirmé.',
            'paiement' => new PaiementEnAttenteResource($paiement),
        ]);
    }

    public function annuler(
        Request $request,
        Paiement $paiement,
        DemandeMesseStatutService $statutService,
    ): JsonResponse {
        $this->ensurePaiementBelongsToParoisse($request, $paiement);

        abort_unless($paiement->statut === 'en_attente', 422, 'Ce paiement ne peut plus être annulé.');

        $paiement->update(['statut' => 'echoue']);

        $demande = $paiement->demandeMesse;
        if ($demande !== null && $demande->statut === 'en_attente') {
            $statutService->mettreAJour($demande, 'annulee', 'Paiement espèces annulé par le secrétariat.');
        }

        return response()->json([
            'message' => 'Paiement annulé.',
        ]);
    }

    private function ensurePaiementBelongsToParoisse(Request $request, Paiement $paiement): void
    {
        $paroisseId = $this->paroisse($request)->id;

        abort_unless(
            $paiement->demandeMesse !== null
                && $paiement->demandeMesse->paroisse_id === $paroisseId,
            404
        );
    }
}
