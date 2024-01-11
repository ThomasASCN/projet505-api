<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Game;
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
public function getGames() {
    $games = Game::all();
    return response()->json($games);
}
public function getValidAds() {
    $now = now();
    $ads = Ad::with(['game', 'user'])
             ->where('end_date', '>=', $now)
             ->where('is_user_validated', false) 
             ->get();
    return response()->json($ads);
}

public function getAcceptedAds() {
    if (Auth::check()) {
        $user = Auth::user();
        $ads = Ad::with(['game', 'user'])
                 ->whereHas('acceptedByUsers', function ($query) use ($user) {
                     $query->where('user_id', $user->id);
                 })
                 ->where('user_id', '!=', $user->id) 
                 ->where('is_user_validated', true)
                 ->get();

        return response()->json($ads);
    }

    return response()->json(['message' => 'Non authentifié'], 401);
}



public function getPostedAds() {
    if (Auth::check()) {
        $user = Auth::user();
        $ads = Ad::with(['game', 'acceptedByUsers'])
                 ->where('user_id', $user->id)
                 ->get();

        foreach ($ads as $ad) {
            $validators = $ad->acceptedByUsers->filter(function ($acceptedUser) use ($user) {
                return $acceptedUser->id != $user->id;
            })->pluck('name');

            $ad->validators = $validators;
        }

        return response()->json($ads);
    }

    return response()->json(['message' => 'Non authentifié'], 401);
}

public function unvalidateAdByUser(Request $request, $adId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::with('acceptedByUsers')->find($adId);

        if ($ad && $ad->acceptedByUsers->contains($user->id) && $ad->is_user_validated) {
            $ad->is_user_validated = false;
            $ad->acceptedByUsers()->detach($user->id); 

            $ad->save();

            return response()->json(['message' => 'Validation de l\'annonce supprimée.'], 200);
        } else {
            return response()->json(['message' => 'Action non autorisée ou annonce déjà non validée.'], 403);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}

public function finalizeAdValidation(Request $request, $adId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if ($ad && $ad->user_id == $user->id) {
            $validatedData = $request->validate([
                'is_valid' => 'required|boolean',
            ]);

            $ad->is_valid = $validatedData['is_valid'];

            if (!$ad->is_valid) {
                $ad->is_user_validated = false;
                $ad->acceptedByUsers()->detach(); 
            }

            $ad->save();

            return response()->json(['message' => 'Statut de l\'annonce mis à jour.'], 200);
        } else {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à gérer cette annonce ou annonce non trouvée.'], 403);
        }
    } else {
        return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
    }
}

public function getDoubleValidatedAds() {
    if (Auth::check()) {
        $user = Auth::user();
        $ads = Ad::with(['game', 'user'])
                 ->where('is_valid', true)
                 ->where('is_user_validated', true)
                 ->where(function ($query) use ($user) {
                     $query->where('user_id', $user->id)
                           ->orWhereHas('acceptedByUsers', function ($subQuery) use ($user) {
                               $subQuery->where('user_id', $user->id);
                           });
                 })
                 ->get();

        return response()->json($ads);
    }

    return response()->json(['message' => 'Non authentifié'], 401);
}

public function unfinalizeAdValidation($adId)
{
    if (Auth::check()) {
        $ad = Ad::find($adId);
        if ($ad) {
            $ad->is_user_validated = false;
            $ad->is_valid = false;
            $ad->save();

            return response()->json(['message' => 'Annulation du ticket.']);
        }

        return response()->json(['message' => 'ticket non trouvé'], 404);
    }

    return response()->json(['message' => 'vous n etes pas autorisé'], 401);
}

public function deleteAd($adId)
{
    if (Auth::check()) {
        $user = Auth::user();
        $ad = Ad::find($adId);

        if (!$ad) {
            return response()->json(['message' => 'Annonce non trouvée.'], 404);
        }

        if ($ad->user_id != $user->id || $ad->is_valid || $ad->is_user_validated) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $ad->delete();
        return response()->json(['message' => 'Annonce supprimée avec succès.'], 200);
    }

    return response()->json(['message' => 'Non authentifié'], 401);
}






}

