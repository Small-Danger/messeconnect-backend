<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TypeOffrande */
class TypeOffrandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'montant_propose' => $this->montant_propose,
            'image' => $this->image,
        ];
    }
}
