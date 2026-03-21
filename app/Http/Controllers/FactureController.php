<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FactureController extends Controller
{
    /**
     * GET api/factures
     * Retourne la liste de toutes les factures
     */
    public function index()
    {
        $factures = Facture::with('abonne')->get();

        return response()->json(['data' => $factures], 200);
    }

    /**
     * GET api/factures/{idf}
     * Consulter une facture par son identifiant
     */
    public function show($idf)
    {
        $facture = Facture::with('abonne')->find($idf);

        if (!$facture) {
            return response()->json(['message' => 'Facture introuvable'], 404);
        }

        return response()->json([
            'data' => [
                'idf'            => $facture->idf,
                'abonne'         => $facture->abonne->nom . ' ' . $facture->abonne->prenom,
                'type_abonement' => $facture->abonne->type_abonement,
                'conso'          => $facture->conso . ' m³',
                'montant_total'  => $facture->montant_total . ' FCFA',
                'date_emission'   => $facture->date_emission,
                'statut'         => $facture->statut,
            ]
        ], 200);
    }

    /**
     * GET api/factures/abonne/{id}
     * Consulter toutes les factures d'un abonné
     */
    public function showByAbonne($id)
    {
        $abonne = Abonne::find($id);

        if (!$abonne) {
            return response()->json(['message' => 'Abonné introuvable'], 404);
        }

        $factures = Facture::where('id', $id)->get();

        if ($factures->isEmpty()) {
            return response()->json([
                'message' => 'Aucune facture trouvée pour cet abonné'
            ], 404);
        }

        return response()->json([
            'data' => [
                'abonne'   => $abonne->nom . ' ' . $abonne->prenom,
                'factures' => $factures->map(function ($facture) {
                    return [
                        'idf'           => $facture->idf,
                        'conso'         => $facture->conso . ' m³',
                        'montant_total' => $facture->montant_total . ' FCFA',
                        'date_emission'  => $facture->date_emission,
                        'statut'        => $facture->statut,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * POST api/factures/calculer
     * Calcule et génère une facture pour un abonné
     */
    public function calculerFacture(Request $request)
    {
        // récupératione des champs Validation
        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer|exists:Abonne,id',
            'conso' => 'required|integer|min:5',

        ], [
            'conso.required' => 'La consommation est obligatoire.',
            'conso.integer'  => 'La consommation doit être un nombre entier en mètres cubes.',
            'conso.min'      => 'La consommation ne peut pas être négative.',
            'id.required'    => 'L\'identifiant de l\'abonné est obligatoire.',
            'id.exists'      => 'L\'abonné spécifié n\'existe pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Données invalides.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $abonne  = Abonne::findOrFail($request->id);
        $conso   = $request->conso;
        $montant = 0;
        $detail  = [];

        // Calcul selon le type d'abonnement
        if ($abonne->type_abonement === 'dommestique') {

            // Tranche 1 : 0 - 10 m³ à 350 FCFA/m³
            if ($conso > 0) {
                $tranche1  = min($conso, 10);
                $montant  += $tranche1 * 350;
                $detail[]  = "{$tranche1} m³ x 350 FCFA = " . ($tranche1 * 350) . " FCFA";
            }

            // Tranche 2 : 11 - 20 m³ à 550 FCFA/m³
            if ($conso > 10) {
                $tranche2  = min($conso - 10, 10);
                $montant  += $tranche2 * 550;
                $detail[]  = "{$tranche2} m³ x 550 FCFA = " . ($tranche2 * 550) . " FCFA";
            }

            // Tranche 3 : au delà de 20 m³ à 780 FCFA/m³
            if ($conso > 20) {
                $tranche3  = $conso - 20;
                $montant  += $tranche3 * 780;
                $detail[]  = "{$tranche3} m³ x 780 FCFA = " . ($tranche3 * 780) . " FCFA";
            }

        } elseif ($abonne->type_abonement === 'professionnel') {

            // Forfait fixe + consommation
            $forfait   = 8500;
            $montConso = $conso * 950;
            $montant   = $forfait + $montConso;
            $detail[]  = "Forfait fixe = 8 500 FCFA";
            $detail[]  = "{$conso} m³ x 950 FCFA = {$montConso} FCFA";

        } else {
            return response()->json([
                'message' => 'Type d\'abonnement inconnu.'
            ], 400);
        }

        // Arrondi à l'entier supérieur
        $montantFinal = (int) ceil($montant);

        // Sauvegarde de la facture
        $facture = Facture::create([
            'id'            => $abonne->id,
            'conso'         => $conso,
            'montant_total' => $montantFinal,
            'date_emission'  => now()->format('Y-m-d'),
            'statut'        => 'Non payé',
        ]);

        return response()->json([
            'message' => 'Facture générée avec succès',
            'data'    => [
                'idf'            => $facture->idf,
                'abonne'         => $abonne->nom . ' ' . $abonne->prenom,
                'type_abonement' => $abonne->type_abonement,
                'conso'          => $conso . ' m³',
                'detail'         => $detail,
                'montant_total'  => $montantFinal . ' FCFA',
                'date_emission'   => $facture->date_emission,
                'statut'         => $facture->statut,
            ]
        ], 201);
    }
}
