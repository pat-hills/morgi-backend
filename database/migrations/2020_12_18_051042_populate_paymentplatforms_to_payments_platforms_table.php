<?php

use App\Models\PaymentPlatform;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PopulatePaymentplatformsToPaymentsPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //PaymentPlatform::create(['name' => 'paypal', 'description' => 'paypal', 'fields' => 'paypal']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //PaymentPlatform::truncate();
    }
}
