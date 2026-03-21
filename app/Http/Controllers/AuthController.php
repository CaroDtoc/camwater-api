<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * POST api/auth/login
     * Connexion d'un abonné
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'mdp' => 'required|string|min:5',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'mdp.required' => 'Le mot de passe est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Données invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        // cette fonction permet de vériier l'existanac d'un abonné en prenant en paramètre son nom
        $abonne = Abonne::where('nom', $request->nom)->first();

        if (!$abonne || !Hash::check($request->mdp, $abonne->mdp)) {
            return response()->json([
                'message' => 'Nom ou mot de passe incorrect.'
            ], 401);
        }

        // évoque les anciens tokens
        $abonne->tokens()->delete();

        //  Générere un nouveau token
        $token = $abonne->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Connexion réussie',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => [
                'id'             => $abonne->id,
                'nom'            => $abonne->nom,
                'prenom'         => $abonne->prenom,
                'num_compteur'   => $abonne->num_compteur,
                'type_abonement' => $abonne->type_abonement,
            ]
        ], 200);
    }

    /**
     * POST api/auth/logout
     * Déconnexion d'un abonné
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    /**
     * GET api/auth/me
     * Retourne les infos de l'abonné connecté
     */
    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user()
        ], 200);
    }
}
