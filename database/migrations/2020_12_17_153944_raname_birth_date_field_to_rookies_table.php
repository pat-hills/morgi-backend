<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RanameBirthDateFieldToRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rookies', function (Blueprint $table) {
            $table->dropColumn('bith_date');
            $table->date('birth_date')->after('description');
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
            $table->dropColumn('birth_date');
            $table->date('bith_date')->after('description');
        });
    }
}
