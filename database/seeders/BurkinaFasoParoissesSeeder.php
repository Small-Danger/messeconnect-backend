<?php

namespace Database\Seeders;

use App\Models\Diocese;
use App\Models\Paroisse;
use App\Models\UserParoisse;
use Illuminate\Database\Seeder;

/**
 * Catalogue élargi de diocèses et paroisses pour démo / production initiale.
 */
class BurkinaFasoParoissesSeeder extends Seeder
{
    public function run(): void
    {
        $dioceses = $this->seedDioceses();
        $this->seedParoisses($dioceses);
    }

    /**
     * @return array<string, Diocese>
     */
    private function seedDioceses(): array
    {
        $definitions = [
            'ouaga' => [
                'nom' => 'Archidiocèse de Ouagadougou',
                'ville' => 'Ouagadougou',
                'description' => 'Archidiocèse métropolitain de Ouagadougou.',
            ],
            'bobo' => [
                'nom' => 'Diocèse de Bobo-Dioulasso',
                'ville' => 'Bobo-Dioulasso',
                'description' => 'Diocèse de Bobo-Dioulasso.',
            ],
            'koudougou' => [
                'nom' => 'Diocèse de Koudougou',
                'ville' => 'Koudougou',
                'description' => 'Diocèse de Koudougou.',
            ],
            'ouahigouya' => [
                'nom' => 'Diocèse de Ouahigouya',
                'ville' => 'Ouahigouya',
                'description' => 'Diocèse de Ouahigouya.',
            ],
            'dedougou' => [
                'nom' => 'Diocèse de Dédougou',
                'ville' => 'Dédougou',
                'description' => 'Diocèse de Dédougou.',
            ],
            'fada' => [
                'nom' => 'Diocèse de Fada N\'Gourma',
                'ville' => 'Fada N\'Gourma',
                'description' => 'Diocèse de l\'Est burkinabè.',
            ],
        ];

        $result = [];
        foreach ($definitions as $key => $data) {
            $result[$key] = Diocese::query()->updateOrCreate(
                ['nom' => $data['nom']],
                [
                    'ville' => $data['ville'],
                    'pays' => 'Burkina Faso',
                    'description' => $data['description'],
                    'actif' => true,
                ]
            );
        }

        return $result;
    }

    /**
     * @param  array<string, Diocese>  $dioceses
     */
    private function seedParoisses(array $dioceses): void
    {
        $paroisses = [
            // Ouagadougou
            [
                'email' => 'contact@paroisse-saint-pierre.test',
                'diocese_key' => 'ouaga',
                'nom' => 'Paroisse Saint-Pierre',
                'adresse' => 'Avenue de la Démocratie',
                'ville' => 'Ouagadougou',
                'description' => 'Paroisse historique au cœur de la capitale, messes quotidiennes et accueil des pèlerins.',
                'couleur_principale' => '#1e3a5f',
                'logo' => DemoImages::CATHEDRALE,
                'user_email' => 'secretaire@paroisse-saint-pierre.test',
                'user_nom' => 'Marie Ouédraogo',
            ],
            [
                'email' => 'contact@sacre-coeur-ouaga.test',
                'diocese_key' => 'ouaga',
                'nom' => 'Paroisse Sacré-Cœur',
                'adresse' => 'Zone du Bois',
                'ville' => 'Ouagadougou',
                'description' => 'Centre spirituel de la zone sud de Ouagadougou.',
                'couleur_principale' => '#7c3aed',
                'logo' => DemoImages::OUAGADOUGOU,
            ],
            [
                'email' => 'contact@notre-dame-ouaga.test',
                'diocese_key' => 'ouaga',
                'nom' => 'Paroisse Notre-Dame du Perpétuel Secours',
                'adresse' => 'Secteur 10, Goughin',
                'ville' => 'Ouagadougou',
                'description' => 'Communauté paroissiale dynamique, catéchèse et groupes de prière.',
                'couleur_principale' => '#0F6E56',
                'logo' => DemoImages::MESSE,
            ],
            [
                'email' => 'contact@saint-antoine-ouaga.test',
                'diocese_key' => 'ouaga',
                'nom' => 'Paroisse Saint-Antoine de Padoua',
                'adresse' => 'Patte d\'Oie',
                'ville' => 'Ouagadougou',
                'description' => 'Paroisse de quartier avec forte participation des jeunes.',
                'couleur_principale' => '#0369a1',
                'logo' => DemoImages::CATHEDRALE,
            ],
            // Bobo-Dioulasso
            [
                'email' => 'contact@notre-dame-paix.test',
                'diocese_key' => 'bobo',
                'nom' => 'Paroisse Notre-Dame de la Paix',
                'adresse' => 'Quartier Dafra',
                'ville' => 'Bobo-Dioulasso',
                'description' => 'Communauté vivante au cœur de Bobo-Dioulasso.',
                'couleur_principale' => '#0F6E56',
                'logo' => DemoImages::MESSE,
            ],
            [
                'email' => 'contact@saint-jean-bobo.test',
                'diocese_key' => 'bobo',
                'nom' => 'Paroisse Saint-Jean-Baptiste',
                'adresse' => 'Konsa',
                'ville' => 'Bobo-Dioulasso',
                'description' => 'Paroisse missionnaire au service des familles.',
                'couleur_principale' => '#b45309',
                'logo' => DemoImages::MESSE,
            ],
            [
                'email' => 'contact@immaculee-bobo.test',
                'diocese_key' => 'bobo',
                'nom' => 'Paroisse Immaculée Conception',
                'adresse' => 'Sarfalao',
                'ville' => 'Bobo-Dioulasso',
                'description' => 'Liturgie soignée et accompagnement des couples.',
                'couleur_principale' => '#4f46e5',
                'logo' => DemoImages::MESSE,
            ],
            // Koudougou
            [
                'email' => 'contact@saint-joseph-koudougou.test',
                'diocese_key' => 'koudougou',
                'nom' => 'Paroisse Saint-Joseph',
                'adresse' => 'Centre-ville',
                'ville' => 'Koudougou',
                'description' => 'Paroisse dynamique au service des fidèles de Koudougou.',
                'couleur_principale' => '#b45309',
                'logo' => DemoImages::KOUDOUGOU,
            ],
            [
                'email' => 'contact@sainte-anne-koudougou.test',
                'diocese_key' => 'koudougou',
                'nom' => 'Paroisse Sainte-Anne',
                'adresse' => 'Quartier Centre',
                'ville' => 'Koudougou',
                'description' => 'Accueil chaleureux et activités pastorales pour enfants et adultes.',
                'couleur_principale' => '#0F6E56',
                'logo' => DemoImages::KOUDOUGOU,
            ],
            // Ouahigouya
            [
                'email' => 'contact@saint-pierre-ouahigouya.test',
                'diocese_key' => 'ouahigouya',
                'nom' => 'Paroisse Saint-Pierre de Ouahigouya',
                'adresse' => 'Centre-ville',
                'ville' => 'Ouahigouya',
                'description' => 'Paroisse du Nord, messes dominicales très suivies.',
                'couleur_principale' => '#1e3a5f',
                'logo' => DemoImages::CATHEDRALE,
            ],
            [
                'email' => 'contact@saint-vincent-ouahigouya.test',
                'diocese_key' => 'ouahigouya',
                'nom' => 'Paroisse Saint-Vincent de Paul',
                'adresse' => 'Secteur 3',
                'ville' => 'Ouahigouya',
                'description' => 'Œuvres caritatives et intentions de messe.',
                'couleur_principale' => '#0F6E56',
                'logo' => DemoImages::MESSE,
            ],
            // Dédougou
            [
                'email' => 'contact@sacre-coeur-dedougou.test',
                'diocese_key' => 'dedougou',
                'nom' => 'Paroisse Sacré-Cœur de Dédougou',
                'adresse' => 'Marché central',
                'ville' => 'Dédougou',
                'description' => 'Paroisse de la Boucle du Mouhoun.',
                'couleur_principale' => '#7c3aed',
                'logo' => DemoImages::MESSE,
            ],
            // Fada
            [
                'email' => 'contact@saint-paul-fada.test',
                'diocese_key' => 'fada',
                'nom' => 'Paroisse Saint-Paul de Fada',
                'adresse' => 'Avenue principale',
                'ville' => 'Fada N\'Gourma',
                'description' => 'Paroisse de l\'Est, proche des communautés rurales.',
                'couleur_principale' => '#b45309',
                'logo' => DemoImages::MESSE,
            ],
        ];

        $phones = [
            'contact@paroisse-saint-pierre.test' => '+22670000001',
            'contact@sacre-coeur-ouaga.test' => '+22670445566',
            'contact@notre-dame-ouaga.test' => '+22670110022',
            'contact@saint-antoine-ouaga.test' => '+22670220033',
            'contact@notre-dame-paix.test' => '+22670112233',
            'contact@saint-jean-bobo.test' => '+22670223344',
            'contact@immaculee-bobo.test' => '+22670334455',
            'contact@saint-joseph-koudougou.test' => '+22670778899',
            'contact@sainte-anne-koudougou.test' => '+22670889900',
            'contact@saint-pierre-ouahigouya.test' => '+22624556677',
            'contact@saint-vincent-ouahigouya.test' => '+22624667788',
            'contact@sacre-coeur-dedougou.test' => '+22620445566',
            'contact@saint-paul-fada.test' => '+22624455667',
        ];

        foreach ($paroisses as $row) {
            $diocese = $dioceses[$row['diocese_key']];
            $userEmail = $row['user_email'] ?? null;
            $userNom = $row['user_nom'] ?? null;

            $paroisse = Paroisse::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'diocese_id' => $diocese->id,
                    'nom' => $row['nom'],
                    'description' => $row['description'],
                    'telephone' => $phones[$row['email']] ?? '+22670000000',
                    'adresse' => $row['adresse'],
                    'ville' => $row['ville'],
                    'pays' => 'Burkina Faso',
                    'logo' => $row['logo'] ?? DemoImages::MESSE,
                    'banniere' => $row['logo'] ?? DemoImages::MESSE,
                    'couleur_principale' => $row['couleur_principale'],
                    'statut' => 'validee',
                    'actif' => true,
                ]
            );

            if ($userEmail !== null) {
                UserParoisse::query()->updateOrCreate(
                    ['email' => $userEmail],
                    [
                        'paroisse_id' => $paroisse->id,
                        'nom' => $userNom,
                        'password' => 'password',
                        'role' => 'secretaire',
                        'actif' => true,
                    ]
                );
            }
        }
    }
}
