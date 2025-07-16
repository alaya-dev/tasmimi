<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin de test
        User::create([
            'email' => 'admin@bitaqati.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Créer un client de test
        User::create([
            'email' => 'client@bitaqati.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
            'email_verified_at' => now(),
        ]);

        // Créer quelques clients supplémentaires
        User::factory(5)->create([
            'role' => User::ROLE_CLIENT,
        ]);
    }
}
