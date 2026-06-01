<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paiement */
class PaiementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_interne' => $this->reference_interne,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'statut_fournisseur' => $this->statut_fournisseur,
            'reference_fournisseur' => $this->reference_fournisseur,
            'telephone_payeur' => $this->telephone_payeur,
            'frais_techniques' => $this->frais_techniques,
            'date_paiement' => $this->date_paiement,
            'date_expiration' => $this->date_expiration,
            'created_at' => $this->created_at,
            'moyen_paiement' => $this->whenLoaded('moyenPaiement', fn () => [
                'id' => $this->moyenPaiement?->id,
                'type' => $this->moyenPaiement?->type,
            ]),
            'demande_messe' => $this->whenLoaded('demandeMesse', function () {
                if ($this->demandeMesse === null) {
                    return null;
                }

                $demande = $this->demandeMesse;
                $utilisateur = null;

                if ($demande->relationLoaded('fidele') && $demande->fidele !== null) {
                    $utilisateur = [
                        'id' => $demande->fidele->id,
                        'nom' => trim($demande->fidele->prenom.' '.$demande->fidele->nom),
                    ];
                } elseif ($demande->nom_demandeur) {
                    $utilisateur = [
                        'id' => null,
                        'nom' => $demande->nom_demandeur,
                    ];
                }

                return [
                    'id' => $demande->id,
                    'reference' => $demande->reference,
                    'utilisateur' => $utilisateur,
                    'paroisse' => $demande->relationLoaded('paroisse') && $demande->paroisse !== null
                        ? ['id' => $demande->paroisse->id, 'nom' => $demande->paroisse->nom]
                        : null,
                ];
            }),
            'campagne_collecte' => $this->whenLoaded('campagneCollecte', function () {
                if ($this->campagneCollecte === null) {
                    return null;
                }

                return [
                    'id' => $this->campagneCollecte->id,
                    'nom' => $this->campagneCollecte->nom,
                    'paroisse' => $this->campagneCollecte->relationLoaded('paroisse') && $this->campagneCollecte->paroisse !== null
                        ? ['id' => $this->campagneCollecte->paroisse->id, 'nom' => $this->campagneCollecte->paroisse->nom]
                        : null,
                ];
            }),
        ];
    }
}
