<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialiteFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('facebook_id')->nullable()->after('description');
            $table->string('google_id')->nullable()->after('description');
            $table->string('signup_source')->default('morgi')->after('id');
            $table->unsignedBigInteger('gender_id')->nullable(true)->change();
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->unsignedBigInteger('interested_in_gender_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('facebook_id');
            $table->dropColumn('google_id');
            $table->dropColumn('signup_source');
            $table->unsignedBigInteger('gender_id')->nullable(false)->change();
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->unsignedBigInteger('interested_in_gender_id')->nullable(false)->change();
        });
    }
}
