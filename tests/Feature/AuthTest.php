<?php

namespace Tests\Feature;

use App\Models\Abonne;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test de connexion réussie
     */
    public function test_login_succes()
    {
        $abonne = Abonne::factory()->create([
            'nom'          => 'Dupont',
            'num_compteur' => Str::random(5),
            'mdp'          => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'nom' => 'Dupont',
            'mdp' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'access_token',
                     'token_type',
                     'data' => [
                         'id',
                         'nom',
                         'prenom',
                         'num_compteur',
                         'type_abonement',
                     ]
                 ])
                 ->assertJsonFragment([
                     'message'    => 'Connexion réussie',
                     'token_type' => 'Bearer',
                 ]);
    }

    /**
     * Test de connexion avec mauvais mot de passe
     */
    public function test_login_mauvais_mdp()
    {
        $abonne = Abonne::factory()->create([
            'nom'          => 'Dupont',
            'num_compteur' => Str::random(5),
            'mdp'          => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'nom' => 'Dupont',
            'mdp' => 'mauvaismdp', // ❌ mauvais mot de passe
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Nom ou mot de passe incorrect.'
                 ]);
    }

    /**
     * Test de connexion avec nom inexistant
     */
    public function test_login_nom_inexistant()
    {
        $response = $this->postJson('/api/auth/login', [
            'nom' => 'NomInexistant',
            'mdp' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Nom ou mot de passe incorrect.'
                 ]);
    }

    /**
     * Test de connexion avec champs manquants
     */
    public function test_login_champs_manquants()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => ['nom', 'mdp']
                 ]);
    }

    /**
     * Test de déconnexion réussie
     */
    public function test_logout_succes()
    {
        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp'          => Hash::make('password123'),
        ]);

        $token = $abonne->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Déconnexion réussie'
                 ]);
    }

    /**
     * Test de déconnexion sans token
     */
    public function test_logout_sans_token()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test de récupération du profil connecté
     */
    public function test_me_succes()
    {
        $abonne = Abonne::factory()->create([
            'num_compteur' => Str::random(5),
            'mdp'          => Hash::make('password123'),
        ]);

        $token = $abonne->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'nom',
                         'prenom',
                         'num_compteur',
                         'type_abonement',
                     ]
                 ]);
    }

    /**
     * Test de récupération du profil sans token
     */
    public function test_me_sans_token()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}
