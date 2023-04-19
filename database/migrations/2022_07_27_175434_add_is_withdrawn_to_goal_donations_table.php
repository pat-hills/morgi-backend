<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsWithdrawnToGoalDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goal_donations', function (Blueprint $table) {
            $table->boolean('is_withdrawn')->default(false)->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goal_donations', function (Blueprint $table) {
            $table->dropColumn('is_withdrawn');
        });
    }
}
