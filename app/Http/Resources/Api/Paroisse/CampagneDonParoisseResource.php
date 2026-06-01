<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paiement */
class CampagneDonParoisseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'reference_interne' => $this->reference_interne,
            'telephone_payeur' => $this->telephone_payeur,
            'date_paiement' => $this->date_paiement,
            'created_at' => $this->created_at,
            'moyen_paiement' => $this->whenLoaded('moyenPaiement', fn () => [
                'id' => $this->moyenPaiement?->id,
                'type' => $this->moyenPaiement?->type,
            ]),
        ];
    }
}
