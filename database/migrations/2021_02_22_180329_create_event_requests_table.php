<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('rookie_id');
            $table->bigInteger('event_audience_id');
            $table->text('description');
            $table->string('phone_number');
            $table->enum('event_type', ['party', 'parade']);
            $table->string('street');
            $table->string('apartment_number');
            $table->string('city');
            $table->string('zip_code');
            $table->string('state');
            $table->bigInteger('country_id');
            $table->integer('guests_count');
            $table->integer('reason');
            $table->text('other_reason');
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
        Schema::dropIfExists('event_requests');
    }
}
