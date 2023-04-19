<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRookiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rookies', function (Blueprint $table) {
            $table->id();
            $table->string('username', 191)->unique();
            $table->bigInteger('gender_id');
            $table->string('first_name', 191);
            $table->string('last_name', 191);
            $table->text('description')->nullable();
            $table->date('bith_date');
            $table->bigInteger('country_id');
            $table->bigInteger('region_id');
            $table->float('morgi_balance')->default(0);
            $table->float('micro_morgi_balance')->default(0);
            $table->float('withdrawal_balance')->default(0);
            $table->string('street', 255)->nullable();
            $table->string('apartment_number', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('zip_code', 255)->nullable();
            $table->string('phone_number', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rookies');
    }
}
