<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePubnubErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pubnub_errors', function (Blueprint $table) {
            $table->id();
            $table->string('api_name');
            $table->string('status_code')->nullable();
            $table->text('users')->nullable();
            $table->text('channels')->nullable();
            $table->text('channels_groups')->nullable();
            $table->text('message')->nullable();
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
        Schema::dropIfExists('pubnub_errors');
    }
}
