<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            BurkinaFasoParoissesSeeder::class,
            FideleSeeder::class,
        ]);

        if (! app()->environment('testing')) {
            $this->call([
                ParoisseDemoSeeder::class,
                FideleDemoSeeder::class,
            ]);
        }

        $this->command?->newLine();
        $this->command?->info('Comptes de démonstration MesseConnect (mot de passe : password)');
        $this->command?->table(
            ['Acteur', 'Email', 'Endpoint login'],
            [
                ['Super Admin', 'admin@messeconnect.test', 'POST /api/admin/login'],
                ['Paroisse', 'secretaire@paroisse-saint-pierre.test', 'POST /api/paroisse/login'],
                ['Fidèle', 'fidele@messeconnect.test', 'POST /api/fidele/login'],
            ]
        );
    }
}
