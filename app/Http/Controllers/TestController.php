<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Avis;

class TestController extends Controller
{
    public function testRoute()
    {
       
// Création d'un utilisateur
$user = User::create([
    'name' => 'Jhn Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
]);
}

public function Annonce()
{
// Création d'une annonce pour l'utilisateur
$annonce = $user->annonces()->create([
    'title' => 'Annonce 1',
    'description' => 'Description de l\'annonce',
]);

}

}

