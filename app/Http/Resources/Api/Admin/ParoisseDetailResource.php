<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;

/** @mixin \App\Models\Paroisse */
class ParoisseDetailResource extends ParoisseResource
{
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'galerie' => $this->whenLoaded('medias', fn () => $this->medias
                ->pluck('url')
                ->filter()
                ->values()
                ->all()),
            'demandes' => $this->whenLoaded('demandes', fn () => $this->demandes
                ->map(fn ($demande) => [
                    'reference' => $demande->reference,
                    'fidele' => $demande->est_anonyme
                        ? 'Anonyme'
                        : ($demande->relationLoaded('fidele') && $demande->fidele !== null
                            ? trim($demande->fidele->prenom.' '.$demande->fidele->nom)
                            : ($demande->nom_demandeur ?? '—')),
                    'montant' => (float) $demande->montant,
                    'date' => $demande->created_at?->toDateString(),
                    'statut' => $demande->statut,
                ])
                ->values()
                ->all()),
            'paiements' => PaiementResource::collection($this->whenLoaded('paiements')),
            'campagnes' => $this->whenLoaded('campagneCollectes', fn () => $this->campagneCollectes
                ->map(fn ($c) => [
                    'titre' => $c->nom,
                    'objectif' => (float) $c->objectif_total,
                    'collecte' => (float) $c->montant_collecte,
                ])
                ->values()
                ->all()),
            'publications' => $this->whenLoaded('publications', fn () => $this->publications
                ->map(fn ($p) => [
                    'titre' => $p->titre,
                    'date' => ($p->date_publication ?? $p->created_at)?->toDateString(),
                ])
                ->values()
                ->all()),
            'historique' => $this->whenLoaded('userParoisses', function () {
                $responsable = $this->userParoisses->first();

                return array_values(array_filter([
                    $this->created_at ? [
                        'date' => $this->created_at->toDateString(),
                        'action' => 'Inscription reçue',
                        'auteur' => $responsable
                            ? trim($responsable->prenom.' '.$responsable->nom)
                            : 'Secrétariat paroisse',
                    ] : null,
                    $this->statut === 'validee' ? [
                        'date' => $this->created_at?->toDateString() ?? now()->toDateString(),
                        'action' => 'Paroisse validée',
                        'auteur' => 'Administration MesseConnect',
                    ] : null,
                    $this->statut === 'rejetee' ? [
                        'date' => $this->created_at?->toDateString() ?? now()->toDateString(),
                        'action' => 'Inscription refusée',
                        'auteur' => 'Administration MesseConnect',
                    ] : null,
                ]));
            }),
        ]);
    }
}
