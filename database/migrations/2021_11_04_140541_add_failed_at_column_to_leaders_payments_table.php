<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFailedAtColumnToLeadersPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->timestamp('failed_at')->after('ccbill_failureCode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_payments', function (Blueprint $table) {
            $table->dropColumn('failed_at');
        });
    }
}
