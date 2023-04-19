<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['chat', 'gift', 'bonus', 'edit', 'bought_micromorgi', 'refund', 'withdrawal', 'withdrawal_rejected']);
            $table->bigInteger('external_id');
            $table->bigInteger('referal_external_id')->nullable();
            $table->bigInteger('to_user_id');
            $table->bigInteger('from_user_id')->nullable();
            $table->bigInteger('amount_micromorgi')->default(0);
            $table->bigInteger('amount_morgi')->default(0);
            $table->enum('refund_type', ['void', 'chargeback', 'refund'])->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->timestamp('refund_date')->nullable();
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
        Schema::dropIfExists('activities_logs');
    }
}
