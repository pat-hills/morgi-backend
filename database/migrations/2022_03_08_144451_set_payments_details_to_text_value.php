<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetPaymentsDetailsToTextValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments_rookies', function (Blueprint $table) {
            $table->text('reference')->after('rookie_id')->change();
        });

        Schema::table('payments_platforms_rookies', function (Blueprint $table) {
            $table->text('payment_info')->after('payment_platform_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
