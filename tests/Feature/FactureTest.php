<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class FactureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Crée un abonné authentifié et retourne le token
     */
    private function getAuthToken(string $type = 'dommestique'): array
    {
        $abonne = Abonne::factory()->create([
            'num_compteur'   => Str::random(5),
            'type_abonement' => $type,
            'mdp'            => Hash::make('password123'),
        ]);

        $token = $abonne->createToken('auth_token')->plainTextToken;

        return ['abonne' => $abonne, 'token' => $token];
    }

    /**
     * Test de la liste de toutes les factures
     */
    public function test_index_factures()
    {
        $auth = $this->getAuthToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson('/api/factures');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data'
                 ]);
    }

    /**
     * Test de consultation d'une facture par son identifiant
     */
    public function test_show_facture()
    {
        $auth = $this->getAuthToken();

        $facture = Facture::create([
            'id'            => $auth['abonne']->id,
            'conso'         => 15,
            'montant_total' => 6250,
            'date_emission' => '2026-01-15',
            'statut'        => 'Non payé',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson("/api/factures/{$facture->idf}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'idf',
                         'abonne',
                         'type_abonement',
                         'conso',
                         'montant_total',
                         'date_emission',
                         'statut',
                     ]
                 ]);
    }

    /**
     * Test de consultation des factures d'un abonné
     */
    public function test_show_factures_by_abonne()
    {
        $auth = $this->getAuthToken();

        Facture::create([
            'id'            => $auth['abonne']->id,
            'conso'         => 10,
            'montant_total' => 3500,
            'date_emission' => '2026-01-15',
            'statut'        => 'Payé',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson("/api/factures/abonne/{$auth['abonne']->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'abonne',
                         'factures' => [
                             '*' => [
                                 'idf',
                                 'conso',
                                 'montant_total',
                                 'date_emission',
                                 'statut',
                             ]
                         ]
                     ]
                 ]);
    }

    /**
     * Test de calcul d'une facture domestique
     */
    public function test_calculer_facture_dommestique()
    {
        $auth = $this->getAuthToken('dommestique');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/factures/calculer', [
            'id'    => $auth['abonne']->id,
            'conso' => 25,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'idf',
                         'abonne',
                         'type_abonement',
                         'conso',
                         'detail',
                         'montant_total',
                         'date_emission',
                         'statut',
                     ]
                 ])
                 ->assertJsonFragment([
                     'montant_total' => '12900 FCFA', // 10x350 + 10x550 + 5x780
                     'statut'        => 'Non payé',
                 ]);
    }

    /**
     * Test de calcul d'une facture professionnelle
     */
    public function test_calculer_facture_professionnelle()
    {
        $auth = $this->getAuthToken('professionnel');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/factures/calculer', [
            'id'    => $auth['abonne']->id,
            'conso' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'idf',
                         'abonne',
                         'type_abonement',
                         'conso',
                         'detail',
                         'montant_total',
                         'date_emission',
                         'statut',
                     ]
                 ])
                 ->assertJsonFragment([
                     'montant_total' => '18000 FCFA', // 8500 + 10x950
                     'statut'        => 'Non payé',
                 ]);
    }

    /**
     * Test de validation — consommation invalide
     */
    public function test_calculer_facture_conso_invalide()
    {
        $auth = $this->getAuthToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/factures/calculer', [
            'id'    => $auth['abonne']->id,
            'conso' => 'abc', // ❌ pas un entier
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => ['conso']
                 ]);
    }

    /**
     * Test de validation — abonné inexistant
     */
    public function test_calculer_facture_abonne_inexistant()
    {
        $auth = $this->getAuthToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->postJson('/api/factures/calculer', [
            'id'    => 99999, // ❌ inexistant
            'conso' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => ['id']
                 ]);
    }

    /**
     * Test — facture introuvable
     */
    public function test_show_facture_introuvable()
    {
        $auth = $this->getAuthToken();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $auth['token'],
        ])->getJson('/api/factures/99999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Facture introuvable']);
    }
}
