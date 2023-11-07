<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->unsignedBigInteger('user_id'); // L'utilisateur qui laisse l'avis
            $table->unsignedBigInteger('reviewed_user_id'); // L'utilisateur qui reçoit l'avis
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reviewed_user_id')->references('id')->on('users');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
