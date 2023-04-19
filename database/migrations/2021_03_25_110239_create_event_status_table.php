<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_status', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        $status = array(
            1 => 'in_pending',
            2 => 'waiting_photos',
            3 => 'pending_photos',
            4 => 'waiting_rookie',
            5 => 'pending_approval',
            6 => 'declined',
            7 => 'declined_merch',
            8 => 'canceled',
            9 => 'fully_funded'
        );

        $DB = \Illuminate\Support\Facades\DB::table('event_status');
        foreach ($status as $key => $value){
            $DB->insert([
                'id' => $key,
                'status' => $value,
                'created_at' =>  \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_status');
    }
}
