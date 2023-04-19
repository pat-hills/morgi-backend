<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptionsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptions_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text('description')->nullable();
            $table->enum('status', ['approved', 'declined', 'to_check'])->default('to_check');
            $table->bigInteger('admin_id')->nullable();
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
        Schema::dropIfExists('descriptions_histories');
    }
}
