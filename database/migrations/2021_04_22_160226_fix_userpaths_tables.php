<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixUserpathsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $subpaths = DB::table('sub_paths')->get();

        Schema::table('paths', function (Blueprint $table) {
            $table->boolean('is_subpath')->default(false);
            $table->bigInteger('created_by')->nullable(true);
            $table->bigInteger('parent_id')->nullable(true);
            $table->softDeletes();
        });

        foreach ($subpaths as $subpath){
            \App\Models\Path::create(['name' => $subpath->name, 'key_name' => $subpath->name,
                'created_by' => $subpath->created_by, 'parent_id' => $subpath->path_id, 'is_subpath' => true]);
        }

        Schema::table('users_paths', function (Blueprint $table) {
            $table->renameColumn('topic_id', 'path_id');
            $table->boolean('is_subpath')->default(false)->after('id');
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::dropIfExists('users_sub_paths');
        Schema::dropIfExists('sub_paths');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->dropColumn('is_subpath');
            $table->dropColumn('created_by');
            $table->dropColumn('parent_id');
            $table->dropSoftDeletes();
        });

        Schema::table('users_paths', function (Blueprint $table) {
            $table->renameColumn('path_id', 'topic_id');
            $table->dropColumn('is_subpath');
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
