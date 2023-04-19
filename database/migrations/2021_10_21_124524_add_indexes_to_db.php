<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->index('leader_id');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->index('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['active', 'id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['leader_id', 'status']);
            $table->index(['rookie_id', 'status']);
            $table->index(['rookie_id', 'status', 'leader_id']);
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
            $table->dropIndex('rookies_seen_leader_id_index');
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->dropIndex('photos_user_id_index');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex('videos_user_id_index');
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->dropIndex('users_paths_user_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['active', 'id']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['leader_id', 'status']);
            $table->dropIndex(['rookie_id', 'status']);
            $table->dropIndex(['rookie_id', 'status', 'leader_id']);
        });

    }
}
