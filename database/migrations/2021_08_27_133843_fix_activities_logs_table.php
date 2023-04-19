<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixActivitiesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('initiated_by')->after('id')->nullable(true);
            $table->dropColumn('refund_type');
        });

        Schema::table('activities_logs', function (Blueprint $table) {
            $table->string('refund_type')->after('id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropColumn('refund_type');
        });

        Schema::table('activities_logs', function (Blueprint $table) {
            $table->dropColumn('initiated_by');
            $table->string('type')->after('id')->nullable(true);
            $table->string('refund_type')->after('id')->nullable(true);
        });
    }
}
