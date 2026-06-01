<?php

namespace App\Services\Fidele;

use App\Models\CampagneCollecte;
use App\Models\DemandeMesse;
use App\Models\HistoriqueDemande;
use App\Models\Messe;
use App\Models\MoyenPaiement;
use App\Models\Notification;
use App\Models\Paiement;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaiementService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function initierPourDemande(DemandeMesse $demande, array $data): Paiement
    {
        $moyen = MoyenPaiement::query()->findOrFail($data['moyen_paiement_id']);

        if ($moyen->paroisse_id !== $demande->paroisse_id || ! $moyen->actif) {
            throw ValidationException::withMessages([
                'moyen_paiement_id' => ['Moyen de paiement invalide pour cette paroisse.'],
            ]);
        }

        return Paiement::query()->create([
            'demande_messe_id' => $demande->id,
            'moyen_paiement_id' => $moyen->id,
            'montant' => $demande->montant,
            'devise' => 'XOF',
            'statut' => 'en_attente',
            'reference_interne' => $this->genererReferenceInterne(),
            'telephone_payeur' => $data['telephone_payeur'] ?? $demande->telephone_demandeur,
            'date_expiration' => now()->addHours(24),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function initierPourCampagne(CampagneCollecte $campagne, array $data): Paiement
    {
        $moyen = MoyenPaiement::query()->findOrFail($data['moyen_paiement_id']);

        if ($moyen->paroisse_id !== $campagne->paroisse_id || ! $moyen->actif) {
            throw ValidationException::withMessages([
                'moyen_paiement_id' => ['Moyen de paiement invalide pour cette campagne.'],
            ]);
        }

        return Paiement::query()->create([
            'campagne_collecte_id' => $campagne->id,
            'moyen_paiement_id' => $moyen->id,
            'montant' => $data['montant'],
            'devise' => 'XOF',
            'statut' => 'en_attente',
            'reference_interne' => $this->genererReferenceInterne(),
            'telephone_payeur' => $data['telephone_payeur'] ?? null,
            'date_expiration' => now()->addHours(24),
        ]);
    }

    public function confirmer(Paiement $paiement, ?string $referenceFournisseur = null): Paiement
    {
        if ($paiement->statut === 'reussi') {
            return $paiement;
        }

        $paiement->update([
            'statut' => 'reussi',
            'statut_fournisseur' => 'SUCCESS',
            'reference_fournisseur' => $referenceFournisseur ?? $paiement->reference_fournisseur,
            'date_paiement' => now(),
        ]);

        if ($paiement->demande_messe_id !== null) {
            $this->confirmerDemande($paiement);
        }

        if ($paiement->campagne_collecte_id !== null) {
            $campagne = CampagneCollecte::query()->find($paiement->campagne_collecte_id);
            $campagne?->increment('montant_collecte', $paiement->montant);
        }

        return $paiement->fresh();
    }

    private function confirmerDemande(Paiement $paiement): void
    {
        $demande = DemandeMesse::query()->find($paiement->demande_messe_id);

        if ($demande === null) {
            return;
        }

        $statutPrecedent = $demande->statut;
        $demande->update(['statut' => 'confirmee']);

        HistoriqueDemande::query()->create([
            'demande_messe_id' => $demande->id,
            'statut_precedent' => $statutPrecedent,
            'nouveau_statut' => 'confirmee',
            'commentaire' => 'Paiement confirmé.',
        ]);

        $messe = Messe::query()->find($demande->messe_id);
        $messe?->increment('places_reservees');

        if ($demande->fidele_id !== null) {
            Notification::query()->create([
                'fidele_id' => $demande->fidele_id,
                'demande_messe_id' => $demande->id,
                'type' => 'confirmation',
                'titre' => 'Paiement confirmé',
                'contenu' => 'Votre paiement pour la demande '.$demande->reference.' est confirmé.',
                'statut' => 'envoyee',
                'date_envoi' => now(),
            ]);
        }
    }

    private function genererReferenceInterne(): string
    {
        do {
            $reference = 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Paiement::query()->where('reference_interne', $reference)->exists());

        return $reference;
    }
}
