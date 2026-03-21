<?php

namespace Database\Factories;

use App\Models\Abonne;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AbonneFactory extends Factory
{
    protected $model = Abonne::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'ville' => $this->faker->state(),
            'quartier' => $this->faker->state(),
            'num_compteur' => $this->faker->unique()->numerify('##########'),
            'type_abonement' => $this->faker->randomElement(['dommestique', 'professionnel']),
            'mdp' => Hash::make('password123'),
        ];
    }
}
