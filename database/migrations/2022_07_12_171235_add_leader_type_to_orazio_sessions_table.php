<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderTypeToOrazioSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orazio_sessions', function (Blueprint $table) {
            $table->string('leader_type')->nullable()->after('leader_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orazio_sessions', function (Blueprint $table) {
            $table->dropColumn('leader_type');
        });
    }
}
