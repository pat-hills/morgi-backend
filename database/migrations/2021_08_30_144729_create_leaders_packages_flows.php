<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadersPackagesFlows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaders_packages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('leader_id');
            $table->double('amount');
            $table->double('amount_spent')->default(0);
            $table->double('amount_available');
            $table->bigInteger('leader_payment_id')->nullable(true);
            $table->bigInteger('balance_transaction_id')->nullable(true);
        });

        Schema::create('leaders_packages_transactions', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->bigInteger('leader_package_id');
            $table->bigInteger('balance_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaders_packages');
        Schema::dropIfExists('leaders_packages_transactions');
    }
}
