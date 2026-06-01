<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserParoisse */
class UserParoisseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paroisse_id' => $this->paroisse_id,
            'nom' => $this->nom,
            'email' => $this->email,
            'role' => $this->role,
            'actif' => $this->actif,
            'last_login' => $this->last_login,
            'paroisse' => $this->whenLoaded('paroisse'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
