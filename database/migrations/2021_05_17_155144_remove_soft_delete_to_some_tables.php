<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSoftDeleteToSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table("paths", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("rookies", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("rookies_points", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("rookies_saved", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("users", function ($table) {
            $table->dropSoftDeletes();
        });

        Schema::table("users_paths", function ($table) {
            $table->dropSoftDeletes();
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
