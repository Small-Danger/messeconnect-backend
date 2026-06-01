<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;

/** @mixin \App\Models\Fidele */
class FideleDetailResource extends FideleResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'favoris' => $this->whenLoaded('favoris', fn () => $this->favoris
                ->map(fn ($favori) => $favori->paroisse?->nom)
                ->filter()
                ->values()
                ->all()),
            'demandes' => $this->whenLoaded('demandes', fn () => $this->demandes
                ->map(fn ($demande) => [
                    'reference' => $demande->reference,
                    'paroisse' => $demande->paroisse?->nom ?? '—',
                    'montant' => (float) $demande->montant,
                    'date' => $demande->created_at?->toDateString(),
                    'statut' => $demande->statut,
                ])
                ->values()
                ->all()),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'connexions' => $this->whenLoaded('journalAudits', fn () => $this->journalAudits
                ->map(fn ($audit) => [
                    'date' => $audit->created_at?->toIso8601String(),
                    'ip' => $audit->ip_address ?? '—',
                    'appareil' => $audit->action,
                ])
                ->values()
                ->all()),
        ]);
    }
}
