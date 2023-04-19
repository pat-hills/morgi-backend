<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersFaceRecognitionMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaders_face_recognition_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('rookie_id');
            $table->unsignedBigInteger('leader_photo_id');
            $table->unsignedBigInteger('rookie_photo_id');
            $table->timestamps();
        });

        Schema::create('rookies_face_recognition_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rookie_id');
            $table->unsignedBigInteger('to_rookie_id');
            $table->unsignedBigInteger('rookie_photo_id');
            $table->unsignedBigInteger('to_rookie_photo_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaders_face_recognition_matches');
        Schema::dropIfExists('rookies_face_recognition_matches');
    }
}
