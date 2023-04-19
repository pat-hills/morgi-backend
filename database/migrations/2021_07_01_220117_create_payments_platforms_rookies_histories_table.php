<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsPlatformsRookiesHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_platforms_rookies_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->bigInteger('payment_platform_id');
            $table->boolean('is_reset')->default(0);
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
        Schema::dropIfExists('payments_platforms_rookies_histories');
    }
}
