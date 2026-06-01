<?php

namespace App\Services\Fidele;

use App\Models\DemandeMesse;
use App\Models\Fidele;
use App\Models\HistoriqueDemande;
use App\Models\Messe;
use App\Models\Notification;
use App\Models\TypeOffrande;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DemandeMesseService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function creer(array $data, ?Fidele $fidele = null): DemandeMesse
    {
        $messe = Messe::query()->findOrFail($data['messe_id']);
        $typeOffrande = TypeOffrande::query()->findOrFail($data['type_offrande_id']);

        if ($messe->paroisse_id !== $data['paroisse_id']) {
            throw ValidationException::withMessages([
                'messe_id' => ['Cette messe n\'appartient pas à la paroisse sélectionnée.'],
            ]);
        }

        if ($typeOffrande->paroisse_id !== $data['paroisse_id']) {
            throw ValidationException::withMessages([
                'type_offrande_id' => ['Ce type d\'offrande n\'appartient pas à la paroisse sélectionnée.'],
            ]);
        }

        if (! $messe->reservable || $messe->statut === 'annulee') {
            throw ValidationException::withMessages([
                'messe_id' => ['Cette messe n\'accepte pas de réservation.'],
            ]);
        }

        if ($messe->capacite_max !== null && $messe->places_reservees >= $messe->capacite_max) {
            throw ValidationException::withMessages([
                'messe_id' => ['Plus de place disponible pour cette messe.'],
            ]);
        }

        $estAnonyme = (bool) ($data['est_anonyme'] ?? false);

        $demande = DemandeMesse::query()->create([
            'fidele_id' => $fidele?->id,
            'paroisse_id' => $data['paroisse_id'],
            'messe_id' => $messe->id,
            'type_offrande_id' => $typeOffrande->id,
            'reference' => $this->genererReference(),
            'est_anonyme' => $estAnonyme,
            'nom_demandeur' => $data['nom_demandeur'] ?? ($fidele ? $fidele->prenom.' '.$fidele->nom : null),
            'email_demandeur' => $data['email_demandeur'] ?? $fidele?->email,
            'telephone_demandeur' => $data['telephone_demandeur'] ?? $fidele?->telephone,
            'intention' => $data['intention'] ?? null,
            'nom_personne_concernee' => $data['nom_personne_concernee'] ?? null,
            'montant' => $data['montant'],
            'statut' => 'en_attente',
        ]);

        HistoriqueDemande::query()->create([
            'demande_messe_id' => $demande->id,
            'statut_precedent' => null,
            'nouveau_statut' => 'en_attente',
            'commentaire' => 'Demande créée.',
        ]);

        if ($fidele !== null) {
            Notification::query()->create([
                'fidele_id' => $fidele->id,
                'demande_messe_id' => $demande->id,
                'type' => 'confirmation',
                'titre' => 'Demande enregistrée',
                'contenu' => 'Votre demande '.$demande->reference.' a été enregistrée.',
                'statut' => 'envoyee',
                'date_envoi' => now(),
            ]);
        }

        return $demande->load(['messe', 'typeOffrande', 'paroisse']);
    }

    private function genererReference(): string
    {
        do {
            $reference = 'MC-'.now()->format('Y').'-'.strtoupper(Str::random(8));
        } while (DemandeMesse::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
