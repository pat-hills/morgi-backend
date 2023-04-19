<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('notification_type_id');
            $table->string('ref_entity_1', 255)->nullable()->default(null);
            $table->bigInteger('ref_entity_1_id')->nullable()->default(null);
            $table->string('ref_entity_2', 255)->nullable()->default(null);
            $table->bigInteger('ref_entity_2_id')->nullable()->default(null);
            $table->boolean('seen')->default(0);
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
        Schema::dropIfExists('notifications');
    }
}
