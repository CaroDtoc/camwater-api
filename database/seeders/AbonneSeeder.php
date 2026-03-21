<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Abonne;

class AbonneSeeder extends Seeder
{
    public function run(): void
    {
        $abonnes = [
            [
                'nom'            => 'Dupont',
                'prenom'         => 'Jean',
                'ville'          => 'Douala',
                'quartier'       => 'Bonanjo',
                'num_compteur'   => 'CPT-001',
                'type_abonement' => 'dommestique',
                'mdp'            => Hash::make('password123'), 
            ],
            [
                'nom'            => 'Mbarga',
                'prenom'         => 'Paul',
                'ville'          => 'Yaoundé',
                'quartier'       => 'Bastos',
                'num_compteur'   => 'CPT-002',
                'type_abonement' => 'professionnel',
                'mdp'            => Hash::make('password123'),
            ],
            [
                'nom'            => 'Fotso',
                'prenom'         => 'Marie',
                'ville'          => 'Douala',
                'quartier'       => 'Akwa',
                'num_compteur'   => 'CPT-003',
                'type_abonement' => 'dommestique',
                'mdp'            => Hash::make('password123'),
            ],
            [
                'nom'            => 'Biya',
                'prenom'         => 'Claire',
                'ville'          => 'Bafoussam',
                'quartier'       => 'Tamdja',
                'num_compteur'   => 'CPT-004',
                'type_abonement' => 'professionnel',
                'mdp'            => Hash::make('password123'),
            ],
            [
                'nom'            => 'Nkomo',
                'prenom'         => 'Pierre',
                'ville'          => 'Douala',
                'quartier'       => 'Deido',
                'num_compteur'   => 'CPT-005',
                'type_abonement' => 'dommestique',
                'mdp'            => Hash::make('password123'),
            ],
        ];

        foreach ($abonnes as $abonne) {
            Abonne::create($abonne);
        }
    }
}
