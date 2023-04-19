<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->double('dollar_amount');
            $table->boolean('is_default');
        });

        $packages = [
            [10, false],
            [25, true],
            [50, false],
            [100, false],
            [200, false],
            [300, true],
            [400, false],
            [500, false],
            [600, false],
            [700, true],
            [800, false],
            [900, false],
            [1000, false],
            [1500, true],
            [2000, false]
        ];

        foreach ($packages as $package){
            \App\Models\SubscriptionPackage::query()->create(['dollar_amount' => $package[0], 'amount' => $package[0], 'is_default' => $package[1]]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_packages');
    }
}
