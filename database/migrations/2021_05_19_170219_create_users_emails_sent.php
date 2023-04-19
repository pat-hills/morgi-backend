<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersEmailsSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_emails_sent', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ACCOUNT_ACTIVATION', 'PASSWORD_RESET'])->default('ACCOUNT_ACTIVATION');
            $table->bigInteger('user_id');
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
        Schema::dropIfExists('users_emails_sent');
    }
}
