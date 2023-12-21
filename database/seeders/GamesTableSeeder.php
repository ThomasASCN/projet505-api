<?php

namespace Database\Seeders;

// Exemple de GamesTableSeeder.php
use Illuminate\Database\Seeder;
use App\Models\Game;

class GamesTableSeeder extends Seeder
{
    public function run()
    {
        $games = [
            ['name' => 'Jeu 1'],
            ['name' => 'Jeu 2'],
            ['name' => 'Jeu 3'],
            ['name' => 'Jeu 4'],
            // Ajout de nouveau jeu 
        ];

        foreach ($games as $game) {
            Game::create($game);
        }
    }
}
