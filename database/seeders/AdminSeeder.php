<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@messeconnect.test'],
            [
                'nom' => 'Super Admin MesseConnect',
                'password' => 'password',
                'role' => 'super_admin',
                'actif' => true,
            ]
        );
    }
}
