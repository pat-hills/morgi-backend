<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeclineReasonColumnToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            //
            $table->text('reason_decline')->nullable()->default(NULL)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            //
            $table->dropColumn('reason_decline');
        });
    }
}
