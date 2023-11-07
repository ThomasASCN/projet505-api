<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'jeux', 'description', 'start_date', 'end_date', 'user_id'];
    // Relation avec l'utilisateur qui a posté l'annonce
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relation avec les utilisateurs qui ont accepté l'annonce à travers la table pivot "user_ads"
    public function acceptedByUsers() {
        return $this->belongsToMany(User::class, 'user_ads', 'ad_id', 'user_id')
            ->withTimestamps();
    }
}
