<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaulrAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\User::create(['type' => 'admin', 'email' => 'paulr@top4.com', 'status' => 'accepted', 'username' => 'Paul',
            'gender_id' => 1, 'email_verified_at' => now(), 'active' => 1, 'password' => '$2y$10$JDo6HTQezQg4aSnCjuFYJ.RtcTuaanv/Tn9FQkojcQigjAK9aO6KG']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
