<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsForPaymentsPlatforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $epay = \App\Models\PaymentPlatform::query()->where('name','like','epay')->first();
        $epay_fields = '{"wallet_number": "Wallet number"}';
        $epay->update(['fields' => $epay_fields]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //

        $epay = \App\Models\PaymentPlatform::query()->where('name','like','epay')->first();
        $epay_fields = '{"email": "Email"}';
        $epay->update(['fields' => $epay_fields]);
    }
}
