<?php

namespace Tests\Feature;

use App\Models\Abonne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AbonneTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Crée un abonné authentifié et retourne le token
     */
    private function getAuthToken(): array
    {
        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp' => Hash::make('password123'),
        ]);

        $token = $abonne->createToken('auth_token')->plainTextToken;

        return ['abonne' => $abonne, 'token' => $token];
    }

    /**
     * Test de création d'un abonné
     */
    public function test_create_abonne()
    {
        $auth = $this->getAuthToken();

        $abonneData = [
            'nom' => 'Test'.Str::random(5),
            'prenom' => 'Prenom'.Str::random(5),
            'ville' => 'Douala',
            'quartier' => 'Bonanjo',
            'num_compteur' => Str::random(5),
            'type_abonement' => 'dommestique',
            'mdp' => 'password123',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$auth['token'], // ✅ Token ajouté
        ])->postJson('/api/abonnes', $abonneData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'nom',
                    'prenom',
                    'ville',
                    'quartier',
                    'num_compteur',
                    'type_abonement',
                    // ✅ mdp retiré car caché grâce à $hidden
                ],
            ]);
    }

    /**
     * Test de mise à jour d'un abonné
     */
    public function test_update_abonne()
    {
        $auth = $this->getAuthToken();

        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp' => Hash::make('password123'), // ✅ Hashé
        ]);

        $updateData = [
            'nom' => 'Nom'.Str::random(5),
            'prenom' => 'Prenom'.Str::random(5),
            'ville' => 'Ville'.Str::random(5),
            'quartier' => 'Quartier'.Str::random(5),
            'type_abonement' => 'dommestique', // ✅ corrigé type_abonnement → type_abonement
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$auth['token'], // ✅ Token ajouté
        ])->putJson("/api/abonnes/{$abonne->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment($updateData);
    }

    /**
     * Test d'affichage d'un abonné
     */
    public function test_show_abonne() // ✅ corrigé test_show_abonnel → test_show_abonne
    {
        $auth = $this->getAuthToken();

        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp' => Hash::make('password123'), // ✅ Hashé
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$auth['token'], // ✅ Token ajouté
        ])->getJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'nom',
                    'prenom',
                    'ville',
                    'quartier',
                    'num_compteur',
                    'type_abonement',
                    // ✅ mdp retiré car caché grâce à $hidden
                ],
            ]);
    }

    /**
     * Test de suppression d'un abonné
     */
    public function test_delete_abonne()
    {
        $auth = $this->getAuthToken();

        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp' => Hash::make('password123'), // ✅ Hashé
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$auth['token'], // ✅ Token ajouté
        ])->deleteJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Abonné supprimé avec succès']);
    }
}
