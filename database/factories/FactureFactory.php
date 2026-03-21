<?php

namespace Database\Factories;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Factories\Factory;

class FactureFactory extends Factory
{
    protected $model = Facture::class;

    public function definition(): array
    {
        return [
            'id'            => Abonne::factory(), // ✅ Crée un abonné automatiquement
            'conso'         => $this->faker->numberBetween(1, 50),
            'montant_total' => $this->faker->numberBetween(350, 50000),
            'date_emission' => $this->faker->date('Y-m-d'),
            'statut'        => $this->faker->randomElement(['Payé', 'Non payé']),
        ];
    }
}
