<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePhotoIdToRookieSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('photo_id');
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
            $table->bigInteger('photo_id')->nullable()->after('rookie_id');
        });
    }
}
