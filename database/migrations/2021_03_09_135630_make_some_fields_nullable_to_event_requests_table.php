<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeSomeFieldsNullableToEventRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->string('street')->nullable()->change();
            $table->string('apartment_number')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('zip_code')->nullable()->change();
            $table->bigInteger('country_id')->nullable()->change();
            $table->string('description')->nullable()->change();
            $table->string('personal_description')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_requests', function (Blueprint $table) {
            $table->dropColumn('personal_description');
        });
    }
}
