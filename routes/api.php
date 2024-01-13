<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ValidController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
API ROUTE USER 
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Inscription
Route::post('/register', [AuthController::class, 'register']);

// Connexion
Route::post('/login', [AuthController::class, 'login']);

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    // Autres routes protégées

    // Route pour mettre à jour le profil de l'utilisateur
    Route::put('/updateProfile', [AuthController::class, 'updateProfile']);

    // Route pour mettre à jour le mot de passe de l'utilisateur   
    Route::put('/updatePassword', [AuthController::class, 'updatePassword']); 

    
    Route::post('/createAd', [Adcontroller::class, 'store']);

    Route::get('/profile', [AuthController::class, 'getProfile'])->middleware('auth:sanctum');


});
/*
API ROUTE ANNONCE
*/


Route::middleware('auth:sanctum')->group(function () {
// Routes pour l'annonce
Route::post('/ads', [AdController::class, 'store'])->middleware('auth'); // Endpoint pour créer une annonce
Route::post('/ads/{adId}/validate', [AdController::class, 'validateAdByUser'])->middleware('auth'); // Endpoint pour que l'utilisateur valide une annonce

//Route::post('/ad/{adId}/accept', [AdController::class, 'acceptAdByUser']);
Route::put('/ad/{adId}/accept', [AdController::class, 'acceptAdByUser']);
Route::get('/accepted-ads', [AdController::class, 'getAcceptedAds']);
Route::get('/posted-ads', [AdController::class, 'getPostedAds']);
Route::post('/ads/{adId}/unvalidate', [AdController::class, 'unvalidateAdByUser']);
Route::post('/ads/{adId}/finalize-validation', [AdController::class, 'finalizeAdValidation'])->middleware('auth');
Route::get('/double-validated-ads', [AdController::class, 'getDoubleValidatedAds'])->middleware('auth');
Route::post('/ads/{adId}/unfinalize-validation', [AdController::class, 'unfinalizeAdValidation'])->middleware('auth');
Route::delete('/ads/{adId}', [AdController::class, 'deleteAd'])->middleware('auth');


});
Route::get('/games', [AdController::class, 'getGames']);
Route::get('/valid-ads', [AdController::class, 'getValidAds']);


/*
API ROUTE AVIS 
*/
Route::middleware('auth:sanctum')->group(function () {
// Route pour laisser un avis
Route::post('/reviews', [ReviewController::class, 'store']);
// Route pour obtenir les avis d'un utilisateur spécifique
Route::get('/users/{userId}/reviews', [ReviewController::class, 'getUserReviews']);
// Route pour supprimer un avis 
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->middleware('auth:sanctum');

});