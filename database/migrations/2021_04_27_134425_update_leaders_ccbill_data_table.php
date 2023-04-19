<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeadersCcbillDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->string('clientAccnum')->change();
            $table->string('clientSubacc')->change();
            $table->string('subscriptionId')->change();
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
            $table->integer('clientAccnum')->change();
            $table->integer('clientSubacc')->change();
            $table->integer('subscriptionId')->change();
        });
    }
}
