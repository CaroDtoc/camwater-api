<?php

namespace Database\Seeders;

use App\Models\Facture;
use Illuminate\Database\Seeder;

class FactureSeeder extends Seeder
{
    public function run(): void
    {
        $factures = [
            // Abonné 6 - Dupont (dommestique)
            [
                'id' => 6,
                'conso' => 8,
                'montant_total' => 2800,
                'date_emission' => '2026-01-15',
                'statut' => 'Payé',
            ],
            [
                'id' => 6,
                'conso' => 15,
                'montant_total' => 6250,
                'date_emission' => '2026-02-15',
                'statut' => 'Non payé',
            ],

            // Abonné 7 - Mbarga (professionnel)
            [
                'id' => 7,
                'conso' => 20,
                'montant_total' => 27500,
                'date_emission' => '2026-01-20',
                'statut' => 'Payé',
            ],
            [
                'id' => 7,
                'conso' => 35,
                'montant_total' => 41750,
                'date_emission' => '2026-02-20',
                'statut' => 'Non payé',
            ],

            // Abonné 8 - Fotso (dommestique)
            [
                'id' => 8,
                'conso' => 25,
                'montant_total' => 12400,
                'date_emission' => '2026-01-10',
                'statut' => 'Payé',
            ],

            // Abonné 9 - Biya (professionnel)
            [
                'id' => 9,
                'conso' => 10,
                'montant_total' => 18000,
                'date_emission' => '2026-02-10',
                'statut' => 'Non payé',
            ],

            // Abonné 10 - Nkomo (dommestique)
            [
                'id' => 10,
                'conso' => 18,
                'montant_total' => 7900,
                'date_emission' => '2026-01-25',
                'statut' => 'Payé',
            ],
        ];

        foreach ($factures as $facture) {
            Facture::create($facture);
        }
    }
}
