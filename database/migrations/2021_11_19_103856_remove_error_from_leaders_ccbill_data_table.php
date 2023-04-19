<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveErrorFromLeadersCcbillDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->dropColumn('error');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->boolean('error')->nullable(true);
        });
    }
}
