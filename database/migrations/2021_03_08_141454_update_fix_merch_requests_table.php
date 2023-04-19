<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFixMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            $table->dropColumn('merch_product_id');
            $table->enum('merch_product', ['hat', 't-shirt'])->after('id');
            $table->dropColumn('tshirt_size');
            $table->dropColumn('hat_size');
            $table->enum('size', ['classic_small','small','medium','large','extra_large'])->after('merch_product');
        });

        Schema::dropIfExists('merch_products');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            $table->dropColumn('merch_product');
            $table->bigInteger('merch_product_id')->after('id');
            $table->enum('tshirt_size', ['classic_small','small','medium','large','extra_large'])->after('merch_product');
            $table->enum('hat_size', ['classic_small','small','medium','large','extra_large'])->after('tshirt_size');
            $table->dropColumn('size');
        });

        Schema::create('merch_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('key_name', 255);
        });
    }
}
