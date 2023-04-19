<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLogicStatusColumnToEventRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            //
            $table->dropColumn('status');
            $table->tinyInteger('event_status_id')->default(1)->after('date_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            //
            $table->dropColumn('event_status_id');
            $table->enum('status', ['approved', 'declined', 'fully_funded', 'pending'])->default('pending')->after('date_at');
        });
    }
}
