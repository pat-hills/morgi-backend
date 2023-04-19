<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            $table->dropColumn('size');
            $table->string('state')->nullable()->after('zip_code');
            $table->enum('tshirt_size', ['classic small', 'small', 'medium', 'large', 'extra large'])->after('merch_product_id');
            $table->enum('hat_size', ['classic small', 'small', 'medium', 'large', 'extra large'])->after('merch_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            $table->enum('size', ['s', 'm', 'l', 'xl']);
            $table->dropColumn('tshirt_size');
            $table->dropColumn('hat_size');
            $table->dropColumn('state');
        });
    }
}
