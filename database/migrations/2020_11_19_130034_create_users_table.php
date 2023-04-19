<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['rookie', 'leader', 'operator', 'admin'])->default('rookie');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'untrusted', 'blocked'])->default('pending');
            $table->string('email', 191)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->string('remember_token', 100)->nullable();
            $table->string('activation_token', 191)->nullable();
            $table->boolean('active')->default(0);
            $table->string('language', 50)->default('en-US');
            $table->bigInteger('group_id')->nullable()->default(null);
            $table->integer('referral')->nullable()->nullable();
            $table->timestamp('last_login_at');
            $table->timestamp('last_activity_at');
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
        Schema::dropIfExists('users');
    }
}
