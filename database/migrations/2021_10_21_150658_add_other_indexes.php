<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_paths', function (Blueprint $table) {
            $table->index(['path_id', 'is_subpath']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_paths', function (Blueprint $table) {
            $table->dropIndex(['path_id', 'is_subpath']);
        });
    }
}
