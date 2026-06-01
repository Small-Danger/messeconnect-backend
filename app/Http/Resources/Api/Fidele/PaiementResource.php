<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paiement */
class PaiementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'devise' => $this->devise,
            'statut' => $this->statut,
            'reference_interne' => $this->reference_interne,
            'reference_fournisseur' => $this->reference_fournisseur,
            'url_checkout' => $this->url_checkout,
            'date_paiement' => $this->date_paiement,
            'date_expiration' => $this->date_expiration,
        ];
    }
}
