<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
   
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'game_id' => 'required|integer|exists:games,id',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
    
        if (Auth::check()) {
            $ad = new Ad([
                'title' => $request->input('title'),
                'game_id' => $request->input('game_id'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
    
            // Utilisez l'utilisateur actuel pour lier l'annonce à cet utilisateur
            $ad->user_id = Auth::user()->id;
    
            $ad->save();
    
            return response()->json($ad, 201);
        } else {
           
            return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
        }
    }
    public function validateAdByUser(Request $request, $adId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $ad = Ad::find($adId);
    
            if ($ad && $ad->user_id != $user->id && !$ad->is_user_validated) {
                // Validez l'annonce par l'utilisateur
                $ad->acceptedByUsers()->attach($user->id, [
                    'is_user_validated' => true,
                    'owner_id' => $ad->user_id
                ]);
    
                $ad->is_user_validated = true;
                $ad->save();
    
                return response()->json(['message' => 'Validation de l\'annonce par l\'utilisateur réussie.'], 200);
            } else {
                return response()->json(['message' => 'Action non autorisée ou annonce déjà validée.'], 403);
            }
        } else {
            return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
        }
    }
    
    
// AdController.php

public function acceptAdByUser(Request $request, $adId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if ($ad) {
            if ($user->id === $ad->user_id) { // Vérifiez si l'utilisateur est le propriétaire de l'annonce
                $validatedData = $request->validate([
                    'is_accepted' => 'required|boolean',
                ]);

                $isAccepted = $validatedData['is_accepted'];

                if ($ad->is_user_validated) { // Vérifiez si l'annonce a été validée par un utilisateur
                    // Mise à jour de l'état de l'annonce
                    $ad->is_valid = $isAccepted;
                    $ad->save();

                    // Mise à jour de la table pivot pour tous les utilisateurs qui ont validé cette annonce
                    foreach ($ad->acceptedByUsers as $validatorUser) {
                        $ad->acceptedByUsers()->updateExistingPivot($validatorUser->id, ['is_accepted' => $isAccepted]);
                    }

                    return response()->json(['message' => 'L\'annonce a été ' . ($isAccepted ? 'acceptée' : 'refusée') . ' par le propriétaire.'], 200);
                } else {
                    return response()->json(['message' => 'Cette annonce n\'a pas été validée par un utilisateur.'], 403);
                }
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à gérer cette annonce.'], 403);
            }
        } else {
            return response()->json(['message' => 'Annonce non trouvée.'], 404);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}



}

/*public function acceptAdByUser(Request $request, $adId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if ($ad) {
            // Acceptez l'annonce par l'utilisateur en mettant à jour la table pivot
            $user->acceptedAds()->attach($adId, ['is_accepted' => true]);
            
            return response()->json(['message' => 'Acceptation de l\'annonce par l\'utilisateur réussie.'], 200);
        } else {
            return response()->json(['message' => 'Annonce non trouvée.'], 404);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}
}





/*public function Bycreator(Request $request, UserAd $user_ad, $adId)
{
    // if (Auth::check()) {
    //     $user = Auth::user();
    //     $ad = Ad::find($adId);

    //     $data = $request->validate([
    //         'is_accepted' => 'boolean'
    //     ]);

    //     // Assurez-vous que $user_ad correspond à l'utilisateur actuel et à l'annonce spécifiée
    //     //if ($user_ad->user_id === $user->id && $user_ad->ad_id === $ad->id) {
    //     //   $user_ad->update($data);

    //     //} else {
    //     //    return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cette proposition.'], 403);
    //    // }
    // } else {
    //     return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    // }

    
}

}


public function acceptOrRejectUserProposal(Request $request, $adId, $userId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if ($ad && $ad->user_id === $user->id && $ad->is_user_validated) {
            // Vérifier si 'is_accepted' est présent dans la requête
            if ($request->has('is_accepted')) {
                $isAccepted = $request->input('is_accepted');

                // Assurer que 'is_accepted' est soit true ou false
                if ($isAccepted !== null && is_bool($isAccepted)) {
                    $ad->acceptedByUsers()->updateExistingPivot($userId, ['is_accepted' => $isAccepted]);

                    // Si le propriétaire accepte, marquez l'annonce comme validée
                    if ($isAccepted) {
                        $ad->is_valid = true;
                        $ad->save();
                    }

                    return response()->json(['message' => 'Proposition d\'utilisateur traitée avec succès.'], 200);
                } else {
                    return response()->json(['message' => 'La valeur de is_accepted est invalide.'], 400);
                }
            } else {
                return response()->json(['message' => 'Le champ is_accepted est requis dans la requête.'], 400);
            }
        } else {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à traiter cette proposition.'], 403);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}



public function rejectUserProposal(Request $request, $adId, $userId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if ($ad && $ad->user_id === $user->id && $ad->is_user_validated) {
            // Mettez à jour la table pivot pour indiquer que le propriétaire rejette la proposition
            $ad->acceptedByUsers()->detach($userId);

            return response()->json(['message' => 'Proposition d\'utilisateur rejetée avec succès.'], 200);
        } else {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à rejeter cette proposition.'], 403);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}



}
*/
