<?php

use App\Http\Controllers\AbonneController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FactureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('abonnes', AbonneController::class);
// Route::post('abonnes', AbonneController::class);
// Route::get('abonnes/{id}', AbonneController::class);
// Route::put('abonnes/{id}', AbonneController::class);
// Route::delete('abonnes/{id}', AbonneController::class);
// Route::apiResource('abonne', AbonneController::class);
// Route::apiResource('abonnes', AbonneController::class);

// Route::prefix('abonnes')->group(function () {
//     Route::get('/',        [AbonneController::class, 'findAll']);   // ✅ findAll() existe
//     Route::get('/{id}',    [AbonneController::class, 'findById']); // ✅ findById() existe
//     Route::put('/{id}',    [AbonneController::class, 'save']);      // ✅ save() existe
//     Route::delete('/{id}', [AbonneController::class, 'delete']);    // ✅ delete() existe
// });

// Routes non protégés
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // Abonnés
    Route::apiResource('abonnes', AbonneController::class);

    // Factures

    Route::prefix('factures')->group(function () {
        Route::get('/', [FactureController::class, 'index']); // lister toutes les factures
        Route::post('/calculer', [FactureController::class, 'calculerFacture']); // Générer une facture
        Route::get('/abonne/{id}', [FactureController::class, 'showByAbonne']); // facture d'un abonné
        Route::get('/{idf}', [FactureController::class, 'show']); // consulter une facture
    });

});
