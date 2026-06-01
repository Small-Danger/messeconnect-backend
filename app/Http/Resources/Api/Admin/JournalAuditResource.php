<?php

namespace App\Http\Resources\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\JournalAudit */
class JournalAuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $acteur = $this->acteur;

        return [
            'id' => $this->id,
            'action' => $this->action,
            'details' => $this->details,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at,
            'acteur' => $acteur instanceof User
                ? [
                    'id' => $acteur->id,
                    'nom' => $acteur->nom,
                    'email' => $acteur->email,
                ]
                : null,
        ];
    }
}
