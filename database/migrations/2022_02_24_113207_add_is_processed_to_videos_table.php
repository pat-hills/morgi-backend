<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsProcessedToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->boolean('is_processed')->default(false)->after('id');
        });

        Schema::table('videos_histories', function (Blueprint $table) {
            $table->boolean('is_processed')->default(false)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('is_processed');
        });

        Schema::table('videos_histories', function (Blueprint $table) {
            $table->dropColumn('is_processed');
        });
    }
}
