<?php

namespace Database\Seeders;

use App\Models\Fidele;
use Illuminate\Database\Seeder;

class FideleSeeder extends Seeder
{
    public function run(): void
    {
        Fidele::query()->updateOrCreate(
            ['email' => 'fidele@messeconnect.test'],
            [
                'nom' => 'Konaté',
                'prenom' => 'Amadou',
                'telephone' => '+22670000002',
                'password' => 'password',
                'ville' => 'Ouagadougou',
                'pays' => 'Burkina Faso',
                'actif' => true,
            ]
        );
    }
}
