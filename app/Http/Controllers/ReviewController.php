<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'reviewed_user_id' => 'required|exists:users,id',
        ]);
    
        $user = Auth::user();
        $reviewedUserId = $request->input('reviewed_user_id');
        $message = $request->input('message');
        
        $review = new Review([
            'message' => $message,
            'user_id' => $user->id,
            'reviewed_user_id' => $reviewedUserId,
        ]);
    
        $review->save();
    
        return response()->json($review, 201);
    }

    public function getUserReviews($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $reviews = Review::where('reviewed_user_id', $userId)->get();

        return response()->json($reviews);
    }
    // suppresion d'avis 
    public function destroy(Review $review)
{
    // Vérifiez si l'utilisateur actuel est l'auteur de l'avis
    if (auth()->user()->id === $review->user_id) {
        $review->delete();
        return response()->json(['message' => 'Avis supprimé avec succès'], 200);
    } else {
        return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer cet avis'], 403);
    }
}

}
