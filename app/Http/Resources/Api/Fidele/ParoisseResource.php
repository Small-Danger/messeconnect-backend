<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Paroisse */
class ParoisseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'site_web' => $this->site_web,
            'logo' => $this->logo,
            'banniere' => $this->banniere,
            'horaires' => $this->when(
                isset($this->horaires_secretariat),
                fn () => $this->horaires_secretariat
            ),
            'couleur_principale' => $this->couleur_principale,
            'diocese' => $this->whenLoaded('diocese'),
            'medias' => MediaParoisseResource::collection($this->whenLoaded('medias')),
            'est_favori' => $this->when(isset($this->est_favori), (bool) $this->est_favori),
        ];
    }
}
