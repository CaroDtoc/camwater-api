<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Abonne;
use App\Models\Facture;

class FactureSeeder extends Seeder
{
    public function run(): void
    {
        //  Récupère les ids réels des abonnés
        $dupont  = Abonne::where('nom', 'Dupont')->first()->id;
        $mbarga  = Abonne::where('nom', 'Mbarga')->first()->id;
        $fotso   = Abonne::where('nom', 'Fotso')->first()->id;
        $biya    = Abonne::where('nom', 'Biya')->first()->id;
        $nkomo   = Abonne::where('nom', 'Nkomo')->first()->id;

        $factures = [
            ['id' => $dupont,  'conso' => 8,  'montant_total' => 2800,  'date_emission' => '2026-01-15', 'statut' => 'Payé'],
            ['id' => $dupont,  'conso' => 15, 'montant_total' => 6250,  'date_emission' => '2026-02-15', 'statut' => 'Non payé'],
            ['id' => $mbarga,  'conso' => 20, 'montant_total' => 27500, 'date_emission' => '2026-01-20', 'statut' => 'Payé'],
            ['id' => $mbarga,  'conso' => 35, 'montant_total' => 41750, 'date_emission' => '2026-02-20', 'statut' => 'Non payé'],
            ['id' => $fotso,   'conso' => 25, 'montant_total' => 12400, 'date_emission' => '2026-01-10', 'statut' => 'Payé'],
            ['id' => $biya,    'conso' => 10, 'montant_total' => 18000, 'date_emission' => '2026-02-10', 'statut' => 'Non payé'],
            ['id' => $nkomo,   'conso' => 18, 'montant_total' => 7900,  'date_emission' => '2026-01-25', 'statut' => 'Payé'],
        ];

        foreach ($factures as $facture) {
            Facture::create($facture);
        }
    }
}
