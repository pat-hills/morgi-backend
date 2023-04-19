<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvgFirstContactAmountToRookiesPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_points', function (Blueprint $table) {
            $table->integer('avg_first_contact_minutes')->default(0)->after('avg_first_contact');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->integer('avg_first_contact_minutes')->default(0)->after('avg_first_contact');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_points', function (Blueprint $table) {
            $table->dropColumn('avg_first_contact_minutes');
        });

        Schema::table('rookies_points_histories', function (Blueprint $table) {
            $table->dropColumn('avg_first_contact_minutes');
        });
    }
}
