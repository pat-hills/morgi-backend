<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('admin_id')->nullable();
            $table->enum('status', ['to_check', 'approved', 'declined'])->default('to_check');
            $table->text('path_location');
            $table->boolean('main')->default(0);
            $table->string('title', 255)->nullable()->default(null);
            $table->text('content')->nullable()->default(null);
            $table->string('tags', 255)->nullable()->default(null);
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
        Schema::dropIfExists('photos_histories');
    }
}
