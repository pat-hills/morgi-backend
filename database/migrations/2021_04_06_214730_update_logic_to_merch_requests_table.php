<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLogicToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_requests', function (Blueprint $table) {
            //
            $table->dropColumn('merch_product');
            $table->dropColumn('size');
            $table->boolean('is_hat')->nullable()->after('type')->default(0);
            $table->boolean('is_tshirt')->nullable()->after('is_hat')->default(0);
            $table->enum('hat_size', ['classic_small', 'small', 'medium', 'large', 'extra_large'])->after('is_tshirt');
            $table->enum('tshirt_size', ['classic_small', 'small', 'medium', 'large', 'extra_large'])->after('hat_size');

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
            //
            $table->dropColumn('is_hat');
            $table->dropColumn('is_tshirt');
            $table->dropColumn('hat_size');
            $table->dropColumn('tshirt_size');
            $table->enum('merch_product', ['hat', 't-shirt'])->after('type');
            $table->enum('size', ['classic_small', 'small', 'medium', 'large', 'extra_large'])->after('merch_product')->nullable();
        });
    }
}
