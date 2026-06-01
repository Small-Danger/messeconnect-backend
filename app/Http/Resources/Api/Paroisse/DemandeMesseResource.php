<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DemandeMesse */
class DemandeMesseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'est_anonyme' => $this->est_anonyme,
            'demandeur' => $this->resolveDemandeur(),
            'intention' => $this->intention,
            'nom_personne_concernee' => $this->nom_personne_concernee,
            'montant' => $this->montant,
            'statut' => $this->statut,
            'messe' => new MesseResource($this->whenLoaded('messe')),
            'type_offrande' => new TypeOffrandeResource($this->whenLoaded('typeOffrande')),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveDemandeur(): array
    {
        if ($this->est_anonyme) {
            return [
                'label' => 'Demande anonyme d\'un fidèle',
            ];
        }

        if ($this->relationLoaded('fidele') && $this->fidele !== null) {
            return [
                'nom' => trim($this->fidele->prenom.' '.$this->fidele->nom),
                'email' => $this->fidele->email,
                'telephone' => $this->fidele->telephone,
            ];
        }

        return [
            'nom' => $this->nom_demandeur,
            'email' => $this->email_demandeur,
            'telephone' => $this->telephone_demandeur,
        ];
    }
}
