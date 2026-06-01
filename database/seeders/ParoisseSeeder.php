<?php

namespace Database\Seeders;

use App\Models\Diocese;
use App\Models\Paroisse;
use App\Models\UserParoisse;
use Illuminate\Database\Seeder;

class ParoisseSeeder extends Seeder
{
    public function run(): void
    {
        $dioceseOuaga = Diocese::query()->updateOrCreate(
            ['nom' => 'Archidiocèse de Ouagadougou'],
            [
                'ville' => 'Ouagadougou',
                'pays' => 'Burkina Faso',
                'description' => 'Archidiocèse métropolitain de Ouagadougou.',
                'actif' => true,
            ]
        );

        $dioceseBobo = Diocese::query()->updateOrCreate(
            ['nom' => 'Diocèse de Bobo-Dioulasso'],
            [
                'ville' => 'Bobo-Dioulasso',
                'pays' => 'Burkina Faso',
                'description' => 'Diocèse de Bobo-Dioulasso.',
                'actif' => true,
            ]
        );

        $dioceseKoudougou = Diocese::query()->updateOrCreate(
            ['nom' => 'Diocèse de Koudougou'],
            [
                'ville' => 'Koudougou',
                'pays' => 'Burkina Faso',
                'description' => 'Diocèse de Koudougou.',
                'actif' => true,
            ]
        );

        $paroisses = [
            [
                'email' => 'contact@paroisse-saint-pierre.test',
                'diocese_id' => $dioceseOuaga->id,
                'nom' => 'Paroisse Saint-Pierre',
                'description' => 'Paroisse historique au cœur de la capitale, accueil chaleureux et messes quotidiennes.',
                'telephone' => '+22670000001',
                'adresse' => 'Avenue de la Démocratie',
                'ville' => 'Ouagadougou',
                'logo' => DemoImages::CATHEDRALE,
                'banniere' => DemoImages::CATHEDRALE,
                'couleur_principale' => '#1e3a5f',
                'user_email' => 'secretaire@paroisse-saint-pierre.test',
                'user_nom' => 'Marie Ouédraogo',
            ],
            [
                'email' => 'contact@notre-dame-paix.test',
                'diocese_id' => $dioceseBobo->id,
                'nom' => 'Paroisse Notre-Dame de la Paix',
                'description' => 'Communauté vivante et accueillante au cœur de Bobo-Dioulasso.',
                'telephone' => '+22670112233',
                'adresse' => 'Quartier Dafra',
                'ville' => 'Bobo-Dioulasso',
                'logo' => DemoImages::MESSE,
                'banniere' => DemoImages::MESSE,
                'couleur_principale' => '#0F6E56',
                'user_email' => null,
                'user_nom' => null,
            ],
            [
                'email' => 'contact@sacre-coeur-ouaga.test',
                'diocese_id' => $dioceseOuaga->id,
                'nom' => 'Paroisse Sacré-Cœur',
                'description' => 'Centre spirituel de la zone sud de Ouagadougou.',
                'telephone' => '+22670445566',
                'adresse' => 'Zone du Bois',
                'ville' => 'Ouagadougou',
                'logo' => DemoImages::OUAGADOUGOU,
                'banniere' => DemoImages::OUAGADOUGOU,
                'couleur_principale' => '#7c3aed',
                'user_email' => null,
                'user_nom' => null,
            ],
            [
                'email' => 'contact@saint-joseph-koudougou.test',
                'diocese_id' => $dioceseKoudougou->id,
                'nom' => 'Paroisse Saint-Joseph',
                'description' => 'Paroisse dynamique au service des fidèles de Koudougou.',
                'telephone' => '+22670778899',
                'adresse' => 'Centre-ville',
                'ville' => 'Koudougou',
                'logo' => DemoImages::KOUDOUGOU,
                'banniere' => DemoImages::KOUDOUGOU,
                'couleur_principale' => '#b45309',
                'user_email' => null,
                'user_nom' => null,
            ],
        ];

        foreach ($paroisses as $data) {
            $paroisse = Paroisse::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'diocese_id' => $data['diocese_id'],
                    'nom' => $data['nom'],
                    'description' => $data['description'],
                    'telephone' => $data['telephone'],
                    'adresse' => $data['adresse'],
                    'ville' => $data['ville'],
                    'pays' => 'Burkina Faso',
                    'logo' => $data['logo'],
                    'banniere' => $data['banniere'],
                    'couleur_principale' => $data['couleur_principale'],
                    'statut' => 'validee',
                    'actif' => true,
                ]
            );

            if ($data['user_email'] !== null) {
                UserParoisse::query()->updateOrCreate(
                    ['email' => $data['user_email']],
                    [
                        'paroisse_id' => $paroisse->id,
                        'nom' => $data['user_nom'],
                        'password' => 'password',
                        'role' => 'secretaire',
                        'actif' => true,
                    ]
                );
            }
        }
    }
}
