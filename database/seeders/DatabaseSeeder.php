<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Ajoute les données de démonstration dans la base.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User'],
        );

        $services = [
            [
                'nom' => 'Plomberie',
                'prix' => 80.00,
            ],
            [
                'nom' => 'Chauffage',
                'prix' => 90.00,
            ],
            [
                'nom' => 'Climatisation',
                'prix' => 100.00,
            ],
            [
                'nom' => 'Pompe à chaleur',
                'prix' => 150.00,
            ],
            [
                'nom' => 'Ventilation',
                'prix' => 75.00,
            ],
        ];

        foreach ($services as $donneesService) {
            Service::updateOrCreate(
                ['nom' => $donneesService['nom']],
                [
                    'prix' => $donneesService['prix'],
                    'elements' => [],
                ],
            );
        }
    }
}
