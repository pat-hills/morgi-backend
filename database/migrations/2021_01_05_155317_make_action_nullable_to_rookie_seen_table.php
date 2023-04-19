<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeActionNullableToRookieSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('action');
        });
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->enum('action', ['saved', 'gifted', 'swiped', 'seen', 'unseen'])->nullable()->after('time_to_choose');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('action');
        });
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->enum('action', ['saved', 'gifted', 'swiped'])->after('time_to_choose');
        });
    }
}
