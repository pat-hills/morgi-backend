<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadersPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaders_paths', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('path_id');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });

        Schema::create('leaders_paths_filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('path_id');
            $table->timestamps();
        });

        Schema::create('actions_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader_id');
            $table->unsignedBigInteger('rookie_id');
            $table->boolean('clicked_profile')->default(false);
            $table->boolean('saw_video')->default(false);
            $table->boolean('saved_profile')->default(false);
            $table->boolean('shared_profile')->default(false);
            $table->boolean('paid_rookie')->default(false);
            $table->unsignedInteger('time_in_rookie_profile_in_seconds')->default(0);
            $table->timestamps();
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('swiped');
            $table->dropColumn('gifted');
            $table->dropColumn('saved');
            $table->dropColumn('clicked');
            $table->dropColumn('shared');
            $table->dropColumn('saw_photos');
            $table->dropColumn('time_in_photos');
            $table->dropColumn('above_the_fold');
            $table->dropColumn('time_to_choose');
            $table->string('source')->nullable()->after('id');
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->dropColumn('swiped');
            $table->dropColumn('gifted');
            $table->dropColumn('saved');
            $table->dropColumn('clicked');
            $table->dropColumn('shared');
            $table->dropColumn('saw_photos');
            $table->dropColumn('time_in_photos');
            $table->dropColumn('above_the_fold');
            $table->dropColumn('time_to_choose');
            $table->string('source')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaders_paths');
        Schema::dropIfExists('leaders_paths_filters');
        Schema::dropIfExists('actions_tracking');

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->string('swiped')->nullable();
            $table->string('gifted')->nullable();
            $table->string('saved')->nullable();
            $table->string('clicked')->nullable();
            $table->string('shared')->nullable();
            $table->string('saw_photos')->nullable();
            $table->string('time_in_photos')->nullable();
            $table->string('above_the_fold')->nullable();
            $table->string('time_to_choose')->nullable();
            $table->dropColumn('source');
        });

        Schema::table('rookies_seen_histories', function (Blueprint $table) {
            $table->string('swiped')->nullable();
            $table->string('gifted')->nullable();
            $table->string('saved')->nullable();
            $table->string('clicked')->nullable();
            $table->string('shared')->nullable();
            $table->string('saw_photos')->nullable();
            $table->string('time_in_photos')->nullable();
            $table->string('above_the_fold')->nullable();
            $table->string('time_to_choose')->nullable();
            $table->dropColumn('source');
        });
    }
}
