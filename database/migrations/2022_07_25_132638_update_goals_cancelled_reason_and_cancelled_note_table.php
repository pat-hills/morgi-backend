<?php

use App\Models\Goal;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGoalsCancelledReasonAndCancelledNoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->string('cancelled_reason')->nullable()->change();
            $table->string('cancelled_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->enum('cancelled_reason',  [
                'goal_not_reached',
                'cancelled_by_user',
                'other'
            ])->nullable()->change();
            $table->dropColumn('cancelled_note');
        });

    }
}
