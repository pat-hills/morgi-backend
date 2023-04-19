<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merch_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->bigInteger('merch_product_id');
            $table->enum('size', ['s', 'm', 'l', 'xl']);
            $table->string('street', 255);
            $table->string('apartment_number', 255);
            $table->string('city', 255);
            $table->string('zip_code', 255);
            $table->bigInteger('country_id');
            $table->string('phone_number', 255);
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
        Schema::dropIfExists('merch_requests');
    }
}
