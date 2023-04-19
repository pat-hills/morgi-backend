<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteLoginsStreakFromRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('logins_streak');
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
            $table->integer('logins_streak')->default(0)->after('likely_receive_score');
        });
    }
}
