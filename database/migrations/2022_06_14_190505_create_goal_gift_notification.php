<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalGiftNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('goal_id')->nullable()->after('ref_user_id');
        });

        \App\Models\NotificationType::query()->create([
            'user_type' => 'rookie',
            'type' => 'transaction_goal',
            'title' => 'Congratulations!',
            'content' => '<ref_username> gave you <amount_micromorgi> Micro Morgi to goal <goal_name>. Goal date ends at <goal_end_date>'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\NotificationType::query()->where('type', 'transaction_goal')->delete();

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('goal_id');
        });
    }
}
