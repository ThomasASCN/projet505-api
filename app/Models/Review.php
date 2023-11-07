<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Relation avec l'utilisateur qui a laissé l'avis
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec l'utilisateur qui a reçu l'avis
    public function reviewedUser() {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }
}
