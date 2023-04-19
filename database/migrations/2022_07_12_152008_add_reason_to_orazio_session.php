<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToOrazioSession extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orazio_sessions', function (Blueprint $table) {
            $table->string('reason')->nullable()->after('session');
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
            $table->dropColumn('reason');
        });
    }
}
