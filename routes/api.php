<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\ReviewController;
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
Route::post('/co',[AuthController::class,'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Autres routes protégées

    // Route pour mettre à jour le profil de l'utilisateur
    Route::put('/updateProfile', [AuthController::class, 'updateProfile']);

    // Route pour mettre à jour le mot de passe de l'utilisateur    
    Route::post('/createAd', [Adcontroller::class, 'store']);
    Route::put('/updatePassword', [AuthController::class, 'updatePassword']);
});
/*
API ROUTE ANNONCE
*/






/*
API ROUTE AVIS 
*/
Route::middleware('auth:sanctum')->group(function () {
// Route pour laisser un avis
Route::post('/reviews', [ReviewController::class, 'store']);

// Route pour obtenir les avis d'un utilisateur spécifique
Route::get('/users/{userId}/reviews', [ReviewController::class, 'getUserReviews']);

Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->middleware('auth:sanctum');

});