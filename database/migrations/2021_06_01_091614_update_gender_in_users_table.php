<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGenderInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('gender_id');
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('gender_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('gender_id')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('rookies', function (Blueprint $table) {
            $table->bigInteger('gender_id')->after('id');
        });


        Schema::table('leaders', function (Blueprint $table) {
            $table->bigInteger('gender_id')->after('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender_id');
        });
    }
}
