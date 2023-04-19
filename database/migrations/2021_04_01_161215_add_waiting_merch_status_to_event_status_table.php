<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWaitingMerchStatusToEventStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_status', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('event_status')->insert([
                'status' => 'merch_in_pending'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_status', function (Blueprint $table) {
            $to_delete = \Illuminate\Support\Facades\DB::table('event_status')->where('status', '=', 'merch_in_pending')->first();
            if($to_delete){
                $to_delete->delete();
            }
        });
    }
}
