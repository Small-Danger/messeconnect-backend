<?php

namespace App\Services\Paroisse;

use App\Models\DemandeMesse;
use App\Models\HistoriqueDemande;

class DemandeMesseStatutService
{
    public function mettreAJour(DemandeMesse $demande, string $nouveauStatut, ?string $commentaire = null): DemandeMesse
    {
        $statutPrecedent = $demande->statut;

        if ($statutPrecedent === $nouveauStatut) {
            return $demande;
        }

        $demande->update(['statut' => $nouveauStatut]);

        HistoriqueDemande::query()->create([
            'demande_messe_id' => $demande->id,
            'statut_precedent' => $statutPrecedent,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $commentaire,
        ]);

        return $demande->fresh();
    }
}
