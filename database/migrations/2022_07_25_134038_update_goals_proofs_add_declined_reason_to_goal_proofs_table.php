<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGoalsProofsAddDeclinedReasonToGoalProofsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goal_proofs', function (Blueprint $table) {
            $table->string('declined_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goal_proofs', function (Blueprint $table) {
            $table->dropColumn('declined_reason');
        });
    }
}
