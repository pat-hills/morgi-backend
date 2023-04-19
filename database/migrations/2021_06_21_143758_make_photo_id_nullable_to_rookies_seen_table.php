<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePhotoIdNullableToRookiesSeenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY photo_id bigint");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY action_at bigint");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY COLUMN clicked tinyint NOT NULL DEFAULT 0");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY COLUMN shared tinyint NOT NULL DEFAULT 0");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY COLUMN saw_photos tinyint NOT NULL DEFAULT 0");

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('action_at');
            $table->boolean('saved')->default(false)->after('seen_at');
            $table->boolean('gifted')->default(false)->after('seen_at');
            $table->boolean('swiped')->default(false)->after('seen_at');
            $table->dropSoftDeletes();
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->enum('action', ['seen','unseen'])->after('time_to_choose')->default('unseen');
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
            $table->dropColumn('action');
        });

        Schema::table('rookies_seen', function (Blueprint $table) {
            $table->enum('action', ['saved','gifted','swiped','seen','unseen'])->after('time_to_choose')->default('unseen');
            $table->timestamp('action_at');
            $table->dropColumn('saved');
            $table->dropColumn('gifted');
            $table->dropColumn('swiped');
            $table->softDeletes();
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY photo_id bigint NOT NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE rookies_seen MODIFY action_at bigint NOT NULL");

    }
}
