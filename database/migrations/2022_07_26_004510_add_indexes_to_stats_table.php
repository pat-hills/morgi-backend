<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->index(['rookie_id', 'created_at', 'deleted_at'], 'index_1');
            $table->index(['rookie_id', 'created_at'], 'index_2');
            $table->index(['rookie_id', 'deleted_at'], 'index_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies_stats', function (Blueprint $table) {
            $table->dropIndex('index_1');
            $table->dropIndex('index_2');
            $table->dropIndex('index_3');
        });
    }
}
