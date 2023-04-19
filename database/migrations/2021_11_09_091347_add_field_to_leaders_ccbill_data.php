<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToLeadersCcbillData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->string("accountingCurrencyCode")->nullable(true);
            $table->string("address1")->nullable(true);
            $table->string("billedCurrencyCode")->nullable(true);
            $table->string("billedInitialPrice")->nullable(true);
            $table->string("billedRecurringPrice")->nullable(true);
            $table->string("bin")->nullable(true);
            $table->string("city")->nullable(true);
            $table->string("dynamicPricingValidationDigest")->nullable(true);
            $table->string("email")->nullable(true);
            $table->string("firstName")->nullable(true);
            $table->string("lastName")->nullable(true);
            $table->string("formName")->nullable(true);
            $table->string("initialPeriod")->nullable(true);
            $table->string("paymentType")->nullable(true);
            $table->string("postalCode")->nullable(true);
            $table->text("priceDescription")->nullable(true);
            $table->string("referringUrl")->nullable(true);
            $table->string("state")->nullable(true);
            $table->string("subscriptionTypeId")->nullable(true);
            $table->string("subscriptionInitialPrice")->nullable(true);
            $table->string("transactionId")->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders_ccbill_data', function (Blueprint $table) {
            $table->dropColumn("accountingCurrencyCode");
            $table->dropColumn("address1");
            $table->dropColumn("billedCurrencyCode");
            $table->dropColumn("billedInitialPrice");
            $table->dropColumn("billedRecurringPrice");
            $table->dropColumn("bin");
            $table->dropColumn("city");
            $table->dropColumn("dynamicPricingValidationDigest");
            $table->dropColumn("email");
            $table->dropColumn("firstName");
            $table->dropColumn("lastName");
            $table->dropColumn("formName");
            $table->dropColumn("initialPeriod");
            $table->dropColumn("paymentType");
            $table->dropColumn("postalCode");
            $table->dropColumn("priceDescription");
            $table->dropColumn("referringUrl");
            $table->dropColumn("state");
            $table->dropColumn("subscriptionTypeId");
            $table->dropColumn("subscriptionInitialPrice");
            $table->dropColumn("transactionId");
        });
    }
}
