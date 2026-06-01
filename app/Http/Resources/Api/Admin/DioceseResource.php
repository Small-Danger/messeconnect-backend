<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Diocese */
class DioceseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'description' => $this->description,
            'logo' => $this->logo,
            'actif' => $this->actif,
            'paroisses_count' => $this->whenCounted('paroisses'),
            'created_at' => $this->created_at,
        ];
    }
}
