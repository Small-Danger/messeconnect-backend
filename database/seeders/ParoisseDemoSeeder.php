<?php

namespace Database\Seeders;

use App\Models\CampagneCollecte;
use App\Models\ConfigurationParoisse;
use App\Models\Messe;
use App\Models\MoyenPaiement;
use App\Models\Paiement;
use App\Models\Paroisse;
use App\Models\Publication;
use App\Models\TypeOffrande;
use Illuminate\Database\Seeder;

class ParoisseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $paroisses = Paroisse::query()
            ->where('statut', 'validee')
            ->where('actif', true)
            ->get();

        foreach ($paroisses as $paroisse) {
            $this->seedOffrandesEtPaiements($paroisse);
            $this->seedMesses($paroisse);
        }

        $this->seedPublications();
        $this->seedCampagnes();
    }

    private function seedOffrandesEtPaiements(Paroisse $paroisse): void
    {
        $types = [
            ['nom' => 'Action de grâce', 'montant_propose' => 10000],
            ['nom' => 'Défunt', 'montant_propose' => 5000],
            ['nom' => 'Mariage', 'montant_propose' => 25000],
            ['nom' => 'Intention', 'montant_propose' => 5000],
        ];

        foreach ($types as $type) {
            TypeOffrande::query()->updateOrCreate(
                ['paroisse_id' => $paroisse->id, 'nom' => $type['nom']],
                [
                    'description' => $type['nom'],
                    'montant_propose' => $type['montant_propose'],
                    'actif' => true,
                ]
            );
        }

        foreach (['orange_money', 'moov_money', 'wave', 'autre'] as $type) {
            MoyenPaiement::query()->updateOrCreate(
                ['paroisse_id' => $paroisse->id, 'type' => $type],
                ['actif' => true, 'environment' => 'sandbox']
            );
        }
    }

    private function seedMesses(Paroisse $paroisse): void
    {
        $messes = [
            ['titre' => 'Messe dominicale', 'days' => 7, 'heure' => '09:00:00'],
            ['titre' => 'Messe du soir', 'days' => 7, 'heure' => '18:00:00'],
            ['titre' => 'Messe dominicale', 'days' => 14, 'heure' => '09:00:00'],
            ['titre' => 'Messe en semaine', 'days' => 3, 'heure' => '07:00:00'],
        ];

        foreach ($messes as $messe) {
            $date = now()->addDays($messe['days'])->toDateString();

            Messe::query()->updateOrCreate(
                [
                    'paroisse_id' => $paroisse->id,
                    'titre' => $messe['titre'],
                    'date' => $date,
                    'heure' => $messe['heure'],
                ],
                [
                    'reservable' => true,
                    'visible' => true,
                    'statut' => 'planifiee',
                    'capacite_max' => 50,
                    'places_reservees' => 0,
                ]
            );
        }
    }

    private function seedPublications(): void
    {
        $saintPierre = Paroisse::query()->where('email', 'contact@paroisse-saint-pierre.test')->first();
        $sacreCoeur = Paroisse::query()->where('email', 'contact@sacre-coeur-ouaga.test')->first();

        if ($saintPierre !== null) {
            Publication::query()->updateOrCreate(
                ['paroisse_id' => $saintPierre->id, 'titre' => 'Grande messe de Pâques'],
                [
                    'contenu' => 'Venez nombreux célébrer la résurrection du Seigneur. Chants, processions et communion pour toute la famille.',
                    'image' => DemoImages::MESSE,
                    'type' => 'evenement',
                    'date_publication' => now()->subDays(2),
                    'visible' => true,
                ]
            );

            Publication::query()->updateOrCreate(
                ['paroisse_id' => $saintPierre->id, 'titre' => 'Retraite paroissiale'],
                [
                    'contenu' => 'Week-end de retraite spirituelle pour toute la famille. Inscriptions au secrétariat paroissial.',
                    'image' => DemoImages::CATHEDRALE,
                    'type' => 'annonce',
                    'date_publication' => now()->subDays(10),
                    'visible' => true,
                ]
            );
        }

        if ($sacreCoeur !== null) {
            Publication::query()->updateOrCreate(
                ['paroisse_id' => $sacreCoeur->id, 'titre' => 'Groupe de prière du jeudi'],
                [
                    'contenu' => 'Rejoignez-nous chaque jeudi à 18h pour un temps de prière communautaire.',
                    'image' => DemoImages::OUAGADOUGOU,
                    'type' => 'annonce',
                    'date_publication' => now()->subDays(5),
                    'visible' => true,
                ]
            );
        }
    }

    private function seedCampagnes(): void
    {
        $saintPierre = Paroisse::query()->where('email', 'contact@paroisse-saint-pierre.test')->first();
        $notreDame = Paroisse::query()->where('email', 'contact@notre-dame-paix.test')->first();
        $saintJoseph = Paroisse::query()->where('email', 'contact@saint-joseph-koudougou.test')->first();

        if ($saintPierre !== null) {
            $campagne = CampagneCollecte::query()->updateOrCreate(
                ['paroisse_id' => $saintPierre->id, 'nom' => 'Rénovation du clocher'],
                [
                    'description' => 'Contribuez à la restauration du clocher historique de la paroisse Saint-Pierre.',
                    'objectif_total' => 5_000_000,
                    'montant_collecte' => 3_200_000,
                    'image' => DemoImages::CATHEDRALE,
                    'date_fin' => now()->addMonths(4)->toDateString(),
                ]
            );
            $this->seedCampagneDons($campagne, $saintPierre);
            $this->seedProfilPublic($saintPierre, [
                'site_web' => 'https://saint-pierre.bf',
                'horaires' => [
                    'Lun-Ven : 8h - 17h',
                    'Sam : 9h - 12h',
                    'Dim : avant les messes',
                ],
            ]);
        }

        if ($notreDame !== null) {
            CampagneCollecte::query()->updateOrCreate(
                ['paroisse_id' => $notreDame->id, 'nom' => 'Aide aux familles démunies'],
                [
                    'description' => 'Collecte solidaire pour accompagner les familles les plus vulnérables du quartier.',
                    'objectif_total' => 2_000_000,
                    'montant_collecte' => 850_000,
                    'image' => DemoImages::OUAGADOUGOU,
                    'date_fin' => now()->addMonths(2)->toDateString(),
                ]
            );
        }

        if ($saintJoseph !== null) {
            CampagneCollecte::query()->updateOrCreate(
                ['paroisse_id' => $saintJoseph->id, 'nom' => 'Équipement de la salle paroissiale'],
                [
                    'description' => 'Financement de chaises et d\'un système son pour les célébrations et réunions.',
                    'objectif_total' => 1_500_000,
                    'montant_collecte' => 420_000,
                    'image' => DemoImages::KOUDOUGOU,
                    'date_fin' => now()->addMonths(3)->toDateString(),
                ]
            );
        }
    }

    private function seedCampagneDons(CampagneCollecte $campagne, Paroisse $paroisse): void
    {
        $moyens = MoyenPaiement::query()
            ->where('paroisse_id', $paroisse->id)
            ->whereIn('type', ['orange_money', 'wave', 'moov_money'])
            ->get()
            ->keyBy('type');

        $dons = [
            ['ref' => 'PAY-DEMO-CLOCHER-01', 'montant' => 500_000, 'type' => 'orange_money', 'tel' => '+22670123456', 'days' => 2],
            ['ref' => 'PAY-DEMO-CLOCHER-02', 'montant' => 250_000, 'type' => 'wave', 'tel' => '+22676987654', 'days' => 5],
            ['ref' => 'PAY-DEMO-CLOCHER-03', 'montant' => 100_000, 'type' => 'moov_money', 'tel' => '+22678112233', 'days' => 8],
            ['ref' => 'PAY-DEMO-CLOCHER-04', 'montant' => 75_000, 'type' => 'orange_money', 'tel' => null, 'days' => 10],
            ['ref' => 'PAY-DEMO-CLOCHER-05', 'montant' => 50_000, 'type' => 'wave', 'tel' => '+22670199887', 'days' => 0, 'statut' => 'en_attente'],
        ];

        foreach ($dons as $don) {
            $moyen = $moyens->get($don['type']);
            if ($moyen === null) {
                continue;
            }

            $statut = $don['statut'] ?? 'reussi';
            $datePaiement = $statut === 'reussi' ? now()->subDays($don['days']) : null;

            Paiement::query()->updateOrCreate(
                ['reference_interne' => $don['ref']],
                [
                    'campagne_collecte_id' => $campagne->id,
                    'moyen_paiement_id' => $moyen->id,
                    'montant' => $don['montant'],
                    'devise' => 'XOF',
                    'statut' => $statut,
                    'telephone_payeur' => $don['tel'],
                    'date_paiement' => $datePaiement,
                    'date_expiration' => now()->addDay(),
                ]
            );
        }
    }

    /**
     * @param  list<string>  $horaires
     * @param  array{site_web?: string|null, horaires?: list<string>}  $data
     */
    private function seedProfilPublic(Paroisse $paroisse, array $data): void
    {
        if (array_key_exists('site_web', $data)) {
            $paroisse->update(['site_web' => $data['site_web']]);
        }

        if (! empty($data['horaires'])) {
            ConfigurationParoisse::query()->updateOrCreate(
                [
                    'paroisse_id' => $paroisse->id,
                    'cle' => 'horaires_secretariat',
                ],
                [
                    'valeur' => json_encode($data['horaires']),
                ]
            );
        }
    }
}
