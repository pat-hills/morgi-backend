<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_seen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('leader_id');
            $table->bigInteger('rookie_id');
            $table->bigInteger('photo_id');
            $table->timestamp('seen_at');
            $table->integer('clicked')->default(0);
            $table->integer('shared')->default(0);
            $table->integer('saw_photos')->default(0);
            $table->integer('time_in_photos')->default(0);
            $table->integer('above_the_fold')->default(0);
            $table->float('time_to_choose')->default(0);
            $table->enum('action', ['saved', 'gifted', 'swiped']);
            $table->timestamp('action_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies_seen');
    }
}
