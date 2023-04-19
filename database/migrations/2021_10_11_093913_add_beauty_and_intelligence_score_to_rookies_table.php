<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeautyAndIntelligenceScoreToRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->tinyInteger('beauty_score')->default(0)->after('phone_number');
            $table->tinyInteger('intelligence_score')->default(0)->after('beauty_score');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('beauty_score');
            $table->dropColumn('intelligence_score');
        });
    }
}
