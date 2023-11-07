<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\TestController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/blog', function () {
    return ('welcome');
});

Route::get('/blog', function () {
    return [
        'title' => 'Mon premier article',
        'content' => 'Ceci est le contenu de mon article'
    ];
});


Route::get('/blogg', function (Request $request) {
    return [$request->input('name')];
    
});
# Socialite URLs



// La redirection vers le provider
Route::get("redirect/{provider}", [SocialiteController::class,'redirect'])->name('socialite.redirect');

// Le callback du provider
Route::get("callback/{provider}", [SocialiteController::class,'callback'])->name('socialite.redirect');

Route::get('/test', [TestController::class, 'testRoute']);
