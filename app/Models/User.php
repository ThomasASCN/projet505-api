<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



 // Relation avec les annonces (ads)
 public function ads() {
    return $this->hasMany(Ad::class);
}

// Relation avec les avis (reviews)
public function reviews() {
    return $this->hasMany(Review::class, 'user_id');
}

// Relation avec les annonces acceptées (acceptedAds) à travers la table pivot "user_ads"
public function acceptedAds() {
    return $this->belongsToMany(Ad::class, 'user_ads', 'user_id', 'ad_id')
        ->withTimestamps();
}

}
