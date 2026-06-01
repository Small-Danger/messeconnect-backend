<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DemandeMesse */
class DemandeMesseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'est_anonyme' => $this->est_anonyme,
            'intention' => $this->intention,
            'nom_personne_concernee' => $this->nom_personne_concernee,
            'montant' => $this->montant,
            'statut' => $this->statut,
            'paroisse' => new ParoisseResource($this->whenLoaded('paroisse')),
            'messe' => new MesseResource($this->whenLoaded('messe')),
            'type_offrande' => new TypeOffrandeResource($this->whenLoaded('typeOffrande')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
