<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUselessColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->bigInteger('uuid')->nullable();
        });

        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->bigInteger('uuid')->nullable();
        });

        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->bigInteger('uuid')->nullable();
        });
    }
}
