<?php

use App\Models\GoalMedia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoalMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goal_id');
            $table->enum('type',[GoalMedia::TYPE_IMAGE,GoalMedia::TYPE_VIDEO])->default(GoalMedia::TYPE_IMAGE);
            $table->text('path_location');
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
        Schema::dropIfExists('goal_media');
    }
}
