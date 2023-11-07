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
            'jeux' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
    
        if (Auth::check()) {
            $ad = new Ad([
                'title' => $request->input('title'),
                'jeux' => $request->input('jeux'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
    
            // Utilisez l'utilisateur actuel pour lier l'annonce à cet utilisateur
            $ad->user_id = Auth::user()->id;
    
            $ad->save();
    
            return response()->json($ad, 201);
        } else {
            // Gérez le cas où l'utilisateur n'est pas authentifié
            return response()->json(['message' => 'L\'utilisateur n\'est pas authentifié.'], 401);
        }
    }
    
}
