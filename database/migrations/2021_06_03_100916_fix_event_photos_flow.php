<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixEventPhotosFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events_photos_histories', function (Blueprint $table) {
            $table->renameColumn('path', 'path_location');
        });

        Schema::table('events_photos', function (Blueprint $table) {
            $table->dropColumn('photo_id');
            $table->string('path_location')->after('event_id');
            $table->bigInteger('user_id')->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events_photos_histories', function (Blueprint $table) {
            $table->renameColumn('path_location', 'path');
        });

        Schema::table('events_photos', function (Blueprint $table) {
            $table->dropColumn('path_location');
            $table->dropColumn('user_id');
            $table->bigInteger('photo_id')->after('event_id');
        });
    }
}
