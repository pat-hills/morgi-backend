<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOrdersTrackingTableAndLogic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::dropIfExists('orders_tracking');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::create('orders_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('courier')->nullable();
            $table->string('n_tracking', 191)->nullable();
            $table->enum('status', ['in_elaboration', 'in_transit', 'delivered'])->default('in_elaboration');
            $table->text('note')->nullable();
            $table->timestamp('status_update')->useCurrent();
            $table->timestamps();
        });
    }
}
