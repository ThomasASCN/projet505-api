<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;


class SocialiteController extends Controller
{
    // Les tableaux des providers autorisés
    protected $providers = [ "google", "github", "facebook" ];

    # La vue pour les liens vers les providers
    public function loginRegister () {
        return view("socialite.login-register");
    }

    # redirection vers le provider
    public function redirect ($provider) {

        // On vérifie si le provider est autorisé
        if (in_array($provider, $this->providers)) {
            return Socialite::driver($provider)->redirect(); // On redirige vers le provider
        }
        abort(404); // Si le provider n'est pas autorisé
    }

    // Callback du provider
    public function callback ($provider) {



        if (in_array($provider, $this->providers)) {
            // Les informations provenant du provider
            $data = Socialite::driver($provider)->user();

            # Social login - register
            $email = $data->getEmail(); // L'adresse email
            $name = $data->getName(); // le nom

            $user = User::updateOrCreate([
                'email' => $email
            ],[
                'name' => $name
            ]);

            $token = $user->createToken("teste");

            return ['token' => $token->plainTextToken];

        }
        abort(404);
    }
    //
}