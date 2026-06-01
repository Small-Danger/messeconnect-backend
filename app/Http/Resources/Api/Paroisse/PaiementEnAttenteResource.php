<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paiement */
class PaiementEnAttenteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $demande = $this->whenLoaded('demandeMesse');

        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'reference_interne' => $this->reference_interne,
            'telephone_payeur' => $this->telephone_payeur,
            'date_expiration' => $this->date_expiration,
            'demande' => $demande ? [
                'id' => $demande->id,
                'reference' => $demande->reference,
                'intention' => $demande->intention,
                'montant' => $demande->montant,
                'statut' => $demande->statut,
                'est_anonyme' => $demande->est_anonyme,
                'nom' => $demande->est_anonyme
                    ? 'Demande anonyme'
                    : ($demande->nom_demandeur ?? '—'),
                'telephone' => $demande->telephone_demandeur,
                'type_offrande' => $demande->relationLoaded('typeOffrande')
                    ? $demande->typeOffrande?->nom
                    : null,
                'messe' => $demande->relationLoaded('messe') && $demande->messe
                    ? [
                        'id' => $demande->messe->id,
                        'titre' => $demande->messe->titre,
                        'date' => $demande->messe->date?->format('Y-m-d') ?? $demande->messe->date,
                        'heure' => is_string($demande->messe->heure)
                            ? substr($demande->messe->heure, 0, 5)
                            : $demande->messe->heure,
                    ]
                    : null,
            ] : null,
        ];
    }
}
