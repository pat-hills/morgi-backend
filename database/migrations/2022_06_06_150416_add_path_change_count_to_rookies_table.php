<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPathChangeCountToRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->unsignedInteger('path_changes_count')->default(0)->after('age_confirmation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('path_changes_count');
        });
    }
}
