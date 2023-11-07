<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // L'utilisateur qui accepte l'annonce
            $table->unsignedBigInteger('ad_id'); // L'annonce acceptée
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('ad_id')->references('id')->on('ads');
    
            $table->unique(['user_id', 'ad_id']); // Pour s'assurer qu'une annonce n'est acceptée qu'une fois par un utilisateur
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_ads');
    }
}
