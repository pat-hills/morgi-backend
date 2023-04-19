<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TruncateAndRefactoringEventStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*\Illuminate\Support\Facades\DB::table('event_status')->truncate();

        $values = array(
            1 => 'in_pending',
            2 => 'waiting_photos',
            3 => 'pending_photos_approval',
            4 => 'approved',
            5 => 'approved_and_paid',
            6 => 'declined',

        );

        $db = \Illuminate\Support\Facades\DB::table('event_status');
        foreach ($values as $key => $value){
            $db->insert([
                'id' => $key,
                'status' =>$value,
                'created_at' =>  \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*\Illuminate\Support\Facades\DB::table('event_status')->truncate();

        $values = array(
            1 => 'in_pending',
            2 => 'waiting_photos',
            3 => 'pending_photos',
            4 => 'waiting_rookie',
            5 => 'pending_approval',
            6 => 'pending_merch',
            7 => 'approved',
            8 => 'declined',

        );

        $db = \Illuminate\Support\Facades\DB::table('event_status');
        foreach ($values as $key => $value){
            $db->insert([
                'id' => $key,
                'status' =>$value,
                'created_at' =>  \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }*/
    }
}
