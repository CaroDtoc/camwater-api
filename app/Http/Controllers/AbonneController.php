<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AbonneController extends Controller
{
    /**
     * GET api/abonnes
     * Retourne la liste de tous les abonnés
     */
    public function index()
    {
        $abonnes = Abonne::all();

        return response()->json(['data' => $abonnes], 200);
    }

    /**
     * POST api/abonnes
     * Créer un nouvel abonné
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'quartier' => 'required|string|max:255',
            'num_compteur' => 'required|string|max:255|unique:Abonne,num_compteur',
            'type_abonement' => 'required|string|in:dommestique,professionnel',
            'mdp' => 'required|string|min:8', // ✅ Ajouté
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Création échouée.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ✅ Hashage du mdp avant sauvegarde
        $data = $validator->validated();
        $data['mdp'] = Hash::make($data['mdp']);

        $abonne = Abonne::create($data);

        return response()->json([
            'message' => 'Abonné créé avec succès',
            'data' => $abonne,  // mdp automatiquement caché grâce à $hidden
        ], 201);
    }

    /**
     * GET api/abonnes/{id}
     * Retourne un abonné à partir de son identifiant
     */
    public function show($id)
    {
        $abonne = Abonne::find($id);

        if (! $abonne) {
            return response()->json(['message' => 'Abonné introuvable'], 404);
        }

        return response()->json(['data' => $abonne], 200);
    }

    /**
     * PUT api/abonnes/{id}
     * Mise à jour d'un abonné
     */
    public function update(Request $request, $id)
    {
        $abonne = Abonne::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'ville' => 'sometimes|string|max:255',
            'quartier' => 'sometimes|string|max:255',
            'num_compteur' => 'sometimes|string|max:255|unique:Abonne,num_compteur,'.$id,
            'type_abonement' => 'sometimes|string|in:dommestique,professionnel',
            'mdp' => 'sometimes|string|min:8', // ✅ Ajouté
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Mise à jour échouée.',
                'errors' => $validator->errors(),
            ], 422);
        }

        //  Hashage du mdp si modifié
        $data = $validator->validated();
        if (isset($data['mdp'])) {
            $data['mdp'] = Hash::make($data['mdp']);
        }

        $abonne->update($data);

        return response()->json([
            'message' => 'Abonné mis à jour avec succès',
            'data' => $abonne,  // mdp automatiquement caché grâce à $hidden
        ], 200);
    }

    // Supprimer un abonne
    public function destroy($id)
    {
        $abonne = Abonne::find($id);

        if (! $abonne) {
            return response()->json(['message' => 'Abonné introuvable'], 404);
        }

        $abonne->delete();

        return response()->json(['message' => 'Abonné supprimé avec succès'], 200);
    }
}
