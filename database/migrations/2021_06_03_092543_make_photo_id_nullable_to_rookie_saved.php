<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePhotoIdNullableToRookieSaved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_saved', function (Blueprint $table) {
            $table->bigInteger('photo_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_saved', function (Blueprint $table) {
            $table->bigInteger('photo_id')->nullable(false)->change();
        });
    }
}
