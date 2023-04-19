<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesOfTheDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies_of_the_days', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->integer('score');
            $table->double('morgi');
            $table->double('micro_morgi');
            $table->timestamps();
        });

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->double('amount_morgi_last_week', 8, 2)->default(0)->after('morgi_last_week');
            $table->double('amount_micro_morgi_last_week', 8, 2)->default(0)->after('micro_morgi_last_week');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies_of_the_days');

        Schema::table('rookies_points', function (Blueprint $table) {
            $table->dropColumn('amount_morgi_last_week');
            $table->dropColumn('amount_micro_morgi_last_week');
        });
    }
}
