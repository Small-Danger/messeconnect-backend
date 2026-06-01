<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paroisse */
class ParoisseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'diocese_id' => $this->diocese_id,
            'nom' => $this->nom,
            'description' => $this->description,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'logo' => $this->logo,
            'banniere' => $this->banniere,
            'couleur_principale' => $this->couleur_principale,
            'statut' => $this->statut,
            'actif' => $this->actif,
            'diocese' => new DioceseResource($this->whenLoaded('diocese')),
            'user_paroisses' => UserParoisseResource::collection($this->whenLoaded('userParoisses')),
            'demandes_count' => $this->whenCounted('demandes'),
            'favoris_count' => $this->whenCounted('favoris'),
            'montant_collecte' => $this->when(
                $this->montant_collecte !== null,
                fn () => (float) $this->montant_collecte,
            ),
            'created_at' => $this->created_at,
        ];
    }
}
