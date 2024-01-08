<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'game_id', 'description', 'start_date', 'end_date', 'user_id'];
    // Relation avec l'utilisateur qui a posté l'annonce
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function acceptedByUsers() {
        return $this->belongsToMany(User::class, 'user_ads', 'ad_id', 'user_id')
            ->withPivot('is_user_validated', 'is_accepted', 'owner_id')
            ->withTimestamps();
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s', // Inclut l'heure
        'end_date' => 'datetime:Y-m-d H:i:s', // Inclut l'heure
    ];
    

   
}
