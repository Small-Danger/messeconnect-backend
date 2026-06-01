<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MoyenPaiement */
class MoyenPaiementResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'environment' => $this->environment,
            'numero' => $this->numero,
            'identifiant_marchand' => $this->identifiant_marchand,
            'client_id' => $this->client_id,
            'callback_url' => $this->callback_url,
            'notify_url' => $this->notify_url,
            'metadata' => $this->metadata,
            'actif' => $this->actif,
            'has_api_key' => $this->api_key !== null,
            'has_secret_key' => $this->secret_key !== null,
            'has_webhook_secret' => $this->webhook_secret !== null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
