<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAvgFirstResponseTimeMinutesInRookieScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_score', function (Blueprint $table) {
            $table->renameColumn('avg_first_response_time_minutes', 'avg_first_response_time_seconds');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->renameColumn('avg_first_response_time_minutes', 'avg_first_response_time_seconds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_score', function (Blueprint $table) {
            $table->renameColumn('avg_first_response_time_seconds', 'avg_first_response_time_minutes');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->renameColumn('avg_first_response_time_seconds', 'avg_first_response_time_minutes');
        });
    }
}
