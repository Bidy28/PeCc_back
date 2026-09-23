<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\RealisationController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MetierController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');


Route::get('/services', [ServiceController::class, 'index']);

Route::get('/prestations', [PrestationController::class, 'index']);

Route::get('/accueil', [AccueilController::class, 'index']);

Route::get('/realisations', [RealisationController::class, 'index']);

Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/metiers', [MetierController::class, 'index']);

// Route publique et non authentifiée : sans limite, n'importe qui peut
// remplir la table en boucle. 5 envois par minute et par IP.
Route::post('/messages', [MessageController::class, 'envoyerMessage'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/services/{service}/prestations', [PrestationController::class, 'synchroniser']);

    // POST et non PUT : PHP ne décode pas le corps multipart/form-data sur
    // une requête PUT, le fichier n'arriverait jamais jusqu'au contrôleur.
    Route::post('/accueil', [AccueilController::class, 'mettreAJour']);
    Route::post('/services/{service}/masquer', [ServiceController::class, 'masquer']);
    Route::post('/services', [ServiceController::class, 'ajouter']);
    Route::put('/services/{service}', [ServiceController::class, 'modifier']);
    Route::delete('/services/{service}', [ServiceController::class, 'supprimer']);

    // Réalisations : POST aussi pour la modification, le corps est en
    // multipart à cause de l'image.
    Route::post('/realisations', [RealisationController::class, 'ajouter']);
    Route::post('/realisations/{realisation}', [RealisationController::class, 'modifier']);
    Route::delete('/realisations/{realisation}', [RealisationController::class, 'supprimer']);

    // Articles : POST aussi pour la modification, le corps est en multipart
    // à cause de l'image de couverture.
    Route::post('/articles', [ArticleController::class, 'ajouter']);
    Route::post('/articles/{article}', [ArticleController::class, 'modifier']);
    Route::delete('/articles/{article}', [ArticleController::class, 'supprimer']);

    // Détails de savoir-faire rattachés à un service.
    Route::post('/metiers', [MetierController::class, 'ajouter']);
    Route::put('/metiers/{metier}', [MetierController::class, 'modifier']);
    Route::delete('/metiers/{metier}', [MetierController::class, 'supprimer']);

    // Chiffres du tableau de bord, recalculés à chaque appel.
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/messages', [MessageController::class, 'listerMessages']);
    Route::post('/messages/{message}/traiter', [MessageController::class, 'traiter']);
    Route::delete('/messages/{message}', [MessageController::class, 'supprimer']);
});
