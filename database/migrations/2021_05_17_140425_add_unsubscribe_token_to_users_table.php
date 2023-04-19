<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class AddUnsubscribeTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('unsubscribe_token')->nullable(true)->after('activation_token');
        });

        $users = \App\Models\User::whereNull('unsubscribe_token')->get();
        foreach ($users as $user){
            $user->update(['unsubscribe_token' => Crypt::encryptString(uniqid('', true))]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unsubscribe_token');
        });
    }
}
