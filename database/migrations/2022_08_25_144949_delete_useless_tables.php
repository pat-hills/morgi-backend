<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteUselessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('events_actions_history');
        Schema::dropIfExists('events_photos');
        Schema::dropIfExists('events_photos_histories');
        Schema::dropIfExists('events_requests');
        Schema::dropIfExists('events_statuses');
        Schema::dropIfExists('merch_actions_histories');
        Schema::dropIfExists('merch_requests');
        Schema::dropIfExists('parties_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
