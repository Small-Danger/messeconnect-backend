<?php

namespace App\Http\Controllers\Api\Paroisse;

use App\Http\Controllers\Api\Paroisse\Concerns\ResolvesParoisse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Paroisse\StoreIntentionGuichetRequest;
use App\Http\Resources\Api\Paroisse\DemandeMesseResource;
use App\Models\Messe;
use App\Models\MoyenPaiement;
use App\Services\Fidele\DemandeMesseService;
use App\Services\Fidele\PaiementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class IntentionGuichetController extends Controller
{
    use ResolvesParoisse;

    public function store(
        StoreIntentionGuichetRequest $request,
        DemandeMesseService $demandeService,
        PaiementService $paiementService,
    ): JsonResponse {
        $paroisse = $this->paroisse($request);
        $data = $request->validated();

        $messe = Messe::query()->findOrFail($data['messe_id']);
        abort_unless($messe->paroisse_id === $paroisse->id, 404);

        $moyen = MoyenPaiement::query()->findOrFail($data['moyen_paiement_id']);
        if ($moyen->paroisse_id !== $paroisse->id || ! $moyen->actif) {
            throw ValidationException::withMessages([
                'moyen_paiement_id' => ['Moyen de paiement invalide.'],
            ]);
        }

        $estAnonyme = (bool) ($data['est_anonyme'] ?? false);

        $demande = $demandeService->creer([
            'paroisse_id' => $paroisse->id,
            'messe_id' => $data['messe_id'],
            'type_offrande_id' => $data['type_offrande_id'],
            'est_anonyme' => $estAnonyme,
            'nom_demandeur' => $estAnonyme ? null : $data['nom_demandeur'],
            'telephone_demandeur' => $data['telephone_demandeur'],
            'intention' => $data['intention'],
            'montant' => $data['montant'],
        ], null);

        $paiement = $paiementService->initierPourDemande($demande, [
            'moyen_paiement_id' => $moyen->id,
            'telephone_payeur' => $data['telephone_demandeur'],
        ]);

        if ($request->boolean('paiement_recu')) {
            $paiementService->confirmer($paiement);
        }

        $demande->load(['messe', 'typeOffrande', 'fidele', 'paiements.moyenPaiement']);

        return response()->json([
            'message' => $request->boolean('paiement_recu')
                ? 'Intention enregistrée et confirmée.'
                : 'Intention enregistrée. En attente de paiement espèces.',
            'demande' => new DemandeMesseResource($demande),
        ], 201);
    }
}
