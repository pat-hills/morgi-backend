<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesSeenHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_seen_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('leader_id');
            $table->bigInteger('rookie_id');
            $table->bigInteger('photo_id')->nullable(true);
            $table->timestamp('seen_at')->nullable(true);
            $table->boolean('swiped')->default(false);
            $table->boolean('gifted')->default(false);
            $table->boolean('saved')->default(false);
            $table->boolean('clicked')->default(false);
            $table->boolean('shared')->default(false);
            $table->boolean('saw_photos')->default(false);
            $table->integer('time_in_photos')->default(0);
            $table->integer('above_the_fold')->default(0);
            $table->double('time_to_choose')->default(0);
            $table->timestamps();
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('seen_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable(true);
        });

        Schema::dropIfExists('rookies_seen_histories');
    }
}
