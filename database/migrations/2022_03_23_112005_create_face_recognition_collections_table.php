<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceRecognitionCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('face_recognition_collections', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_full')->default(false);
            $table->string('aws_arn');
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
        Schema::dropIfExists('face_recognition_collections');
    }
}
