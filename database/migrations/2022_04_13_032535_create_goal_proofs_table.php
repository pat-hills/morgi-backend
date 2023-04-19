<?php

use App\Models\GoalProof;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalProofsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goal_id');
            $table->enum('type',[GoalProof::TYPE_IMAGE,GoalProof::TYPE_VIDEO])->default(GoalProof::TYPE_IMAGE);
            $table->text('path_location');
            $table->enum('status',[
                GoalProof::STATUS_APPROVED,
                GoalProof::STATUS_DECLINED,
                GoalProof::STATUS_PENDING
            ])->default(GoalProof::STATUS_PENDING);
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
        Schema::dropIfExists('goal_proofs');
    }
}
