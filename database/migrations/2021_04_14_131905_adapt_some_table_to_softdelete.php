<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdaptSomeTableToSoftdelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users_sub_paths', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('rookies', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('points', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('profiles_saved', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('photos', function (Blueprint $table) {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'deleted_at')){
            Schema::table('users', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('videos', 'deleted_at')){
            Schema::table('videos', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('users_sub_paths', 'deleted_at')){
            Schema::table('users_sub_paths', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('users_paths', 'deleted_at')){
            Schema::table('users_paths', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('rookies', 'deleted_at')){
            Schema::table('rookies', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('points', 'deleted_at')){
            Schema::table('points', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('profiles_saved', 'deleted_at')){
            Schema::table('profiles_saved', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('photos', 'deleted_at')){
            Schema::table('photos', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

    }
}
