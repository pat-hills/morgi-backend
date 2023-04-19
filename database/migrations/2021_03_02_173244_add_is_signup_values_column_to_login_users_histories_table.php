<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSignupValuesColumnToLoginUsersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_users_histories', function (Blueprint $table) {
            //
            $table->boolean('is_signup_values')->default(0)->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_users_histories', function (Blueprint $table) {
            //
            $table->dropColumn('is_signup_values');
        });
    }
}
