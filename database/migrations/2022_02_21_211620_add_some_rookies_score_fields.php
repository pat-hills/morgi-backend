<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeRookiesScoreFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_score', function (Blueprint $table) {
            $table->integer('avg_gifts_amounts')->default(0)->after('avg_first_response_time_minutes');
            $table->integer('leaders_retaining_rookie')->default(0)->after('avg_first_response_time_minutes');
            $table->integer('hungry_rookies')->default(0)->after('avg_first_response_time_minutes');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->unsignedDouble('avg_gifts_amounts_in_dollars')->default(0)->after('avg_first_response_time_minutes');
            $table->integer('leaders_retaining_rookie')->default(0)->after('avg_first_response_time_minutes');
            $table->integer('hungry_rookies')->default(0)->after('avg_first_response_time_minutes');
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
            $table->dropColumn('avg_gifts_amounts');
            $table->dropColumn('leaders_retaining_rookie');
            $table->dropColumn('hungry_rookies');
        });

        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->dropColumn('avg_gifts_amounts_in_dollars');
            $table->dropColumn('leaders_retaining_rookie');
            $table->dropColumn('hungry_rookies');
        });
    }
}
