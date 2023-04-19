<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderTypeFieldInRookiesSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->string('leader_type')->nullable()->after('source');
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->string('leader_type')->nullable()->after('source');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('leader_type');
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->dropColumn('leader_type');
        });
    }
}
