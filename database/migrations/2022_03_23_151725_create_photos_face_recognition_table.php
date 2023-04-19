<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosFaceRecognitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos_face_recognition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('photo_id');
            $table->double('confidence');
            $table->string('external_image_id');
            $table->longText('payload');
            $table->timestamps();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->boolean('is_face_recognition_processed')->default(false)->after('main');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('is_face_recognition_processed');
        });

        Schema::dropIfExists('photos_face_recognition');
    }
}
