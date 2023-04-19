<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewGoalStatusToGoalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE goals CHANGE COLUMN status status ENUM('pending', 'active', 'suspended', 'review', 'cancelled', 'successful', 'awaiting_proof', 'proof_pending_approval', 'proof_status_declined') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE goals CHANGE COLUMN status status ENUM('active','cancelled','pending','successful','awaiting_proof','proof_status_declined','proof_pending_approval') NOT NULL DEFAULT 'pending'");
    }
}
