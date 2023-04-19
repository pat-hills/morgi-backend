<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubpathTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_paths', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('created_by');
            $table->bigInteger('path_id');
            $table->timestamps();
        });

        Schema::create('users_sub_paths', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subpath_id');
            $table->bigInteger('user_id');
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
        Schema::dropIfExists('sub_paths');
        Schema::dropIfExists('users_sub_paths');
    }
}
