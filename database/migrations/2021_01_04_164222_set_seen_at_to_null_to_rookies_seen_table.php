<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetSeenAtToNullToRookiesSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('seen_at');
        });
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable()->after('photo_id');
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
            $table->dropColumn('seen_at');
        });
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->timestamp('seen_at')->after('photo_id');
        });
    }
}
