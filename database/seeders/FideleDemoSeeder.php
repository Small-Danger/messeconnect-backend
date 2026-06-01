<?php

namespace Database\Seeders;

use App\Models\DemandeMesse;
use App\Models\FavoriParoisse;
use App\Models\Fidele;
use App\Models\HistoriqueDemande;
use App\Models\Messe;
use App\Models\MoyenPaiement;
use App\Models\Paroisse;
use App\Models\Paiement;
use App\Models\TypeOffrande;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FideleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fidele = Fidele::query()->where('email', 'fidele@messeconnect.test')->first();

        if ($fidele === null) {
            return;
        }

        $saintPierre = Paroisse::query()->where('email', 'contact@paroisse-saint-pierre.test')->first();
        $sacreCoeur = Paroisse::query()->where('email', 'contact@sacre-coeur-ouaga.test')->first();
        $saintJoseph = Paroisse::query()->where('email', 'contact@saint-joseph-koudougou.test')->first();

        if ($saintPierre === null) {
            return;
        }

        foreach ([$saintPierre, $sacreCoeur, $saintJoseph] as $paroisse) {
            if ($paroisse === null) {
                continue;
            }

            FavoriParoisse::query()->updateOrCreate(
                ['fidele_id' => $fidele->id, 'paroisse_id' => $paroisse->id],
                []
            );
        }

        $messeDominicale = Messe::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('titre', 'Messe dominicale')
            ->orderBy('date')
            ->first();

        $messeSemaine = Messe::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('titre', 'Messe en semaine')
            ->orderBy('date')
            ->first();

        $typeIntention = TypeOffrande::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('nom', 'Intention')
            ->first();

        $typeDefunt = TypeOffrande::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('nom', 'Défunt')
            ->first();

        $moyenOrange = MoyenPaiement::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('type', 'orange_money')
            ->first();

        $moyenWave = MoyenPaiement::query()
            ->where('paroisse_id', $saintPierre->id)
            ->where('type', 'wave')
            ->first();

        if ($messeDominicale === null || $typeIntention === null || $moyenOrange === null) {
            return;
        }

        $demandeConfirmee = DemandeMesse::query()->updateOrCreate(
            ['reference' => 'MC-DEMO-2026-CONFIRMEE'],
            [
                'fidele_id' => $fidele->id,
                'paroisse_id' => $saintPierre->id,
                'messe_id' => $messeDominicale->id,
                'type_offrande_id' => $typeIntention->id,
                'est_anonyme' => false,
                'nom_demandeur' => $fidele->prenom.' '.$fidele->nom,
                'email_demandeur' => $fidele->email,
                'telephone_demandeur' => $fidele->telephone,
                'intention' => 'Action de grâce pour ma famille',
                'montant' => 10_000,
                'statut' => 'confirmee',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(4),
            ]
        );

        Paiement::query()->updateOrCreate(
            ['reference_interne' => 'PAY-DEMO-20260525-CONF'],
            [
                'demande_messe_id' => $demandeConfirmee->id,
                'moyen_paiement_id' => $moyenOrange->id,
                'montant' => 10_000,
                'devise' => 'XOF',
                'statut' => 'reussi',
                'statut_fournisseur' => 'SUCCESS',
                'reference_fournisseur' => 'OM-'.Str::upper(Str::random(8)),
                'telephone_payeur' => $fidele->telephone,
                'date_paiement' => now()->subDays(4),
            ]
        );

        if ($messeSemaine !== null && $typeDefunt !== null && $moyenWave !== null) {
            $demandeEnAttente = DemandeMesse::query()->updateOrCreate(
                ['reference' => 'MC-DEMO-2026-ATTENTE'],
                [
                    'fidele_id' => $fidele->id,
                    'paroisse_id' => $saintPierre->id,
                    'messe_id' => $messeSemaine->id,
                    'type_offrande_id' => $typeDefunt->id,
                    'est_anonyme' => true,
                    'nom_demandeur' => null,
                    'email_demandeur' => null,
                    'telephone_demandeur' => null,
                    'intention' => 'Requiem pour feu Jean Ouédraogo',
                    'montant' => 5_000,
                    'statut' => 'en_attente',
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ]
            );

            Paiement::query()->updateOrCreate(
                ['reference_interne' => 'PAY-DEMO-20260529-ATT'],
                [
                    'demande_messe_id' => $demandeEnAttente->id,
                    'moyen_paiement_id' => $moyenWave->id,
                    'montant' => 5_000,
                    'devise' => 'XOF',
                    'statut' => 'en_attente',
                    'telephone_payeur' => $fidele->telephone,
                    'date_expiration' => now()->addDay(),
                ]
            );
        }

        foreach ([$demandeConfirmee, $demandeEnAttente ?? null] as $demande) {
            if ($demande === null) {
                continue;
            }

            HistoriqueDemande::query()->updateOrCreate(
                [
                    'demande_messe_id' => $demande->id,
                    'nouveau_statut' => $demande->statut,
                ],
                [
                    'statut_precedent' => null,
                    'commentaire' => 'Demande créée (données de démonstration).',
                ]
            );
        }

        $messeDominicale->update(['places_reservees' => min(
            ($messeDominicale->places_reservees ?? 0) + 1,
            $messeDominicale->capacite_max ?? 50
        )]);
    }
}
