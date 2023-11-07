<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    // Méthode pour récupérer tous les avis
    public function index()
    {
        $reviews = Review::all();
        return response()->json($reviews);
    }

    // Méthode pour créer un nouvel avis
    public function store(Request $request)
    {
        $review = Review::create($request->all());
        return response()->json($review, 201);
    }

    // Méthode pour récupérer un avis par son ID
    public function show($id)
    {
        $review = Review::findOrFail($id);
        return response()->json($review);
    }

    // Méthode pour mettre à jour un avis
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->all());
        return response()->json($review);
    }

    // Méthode pour supprimer un avis
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        return response()->json(null, 204);
    }


}

