<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxScoreFieldToRookiesOfTheDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_of_the_days', function (Blueprint $table) {
            $table->integer('max_score')->after('score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_of_the_days', function (Blueprint $table) {
            $table->dropColumn('max_score');
        });
    }
}
