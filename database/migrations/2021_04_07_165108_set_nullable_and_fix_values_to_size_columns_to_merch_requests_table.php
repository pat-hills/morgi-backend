<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetNullableAndFixValuesToSizeColumnsToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN hat_size hat_size ENUM('small', 'medium', 'large') DEFAULT NULL");
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN tshirt_size tshirt_size ENUM('classic_small', 'small', 'medium', 'large', 'extra_large') DEFAULT NULL");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN hat_size hat_size ENUM('classic_small', 'small', 'medium', 'large', 'extra_large') NOT NULL");
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN tshirt_size tshirt_size ENUM('classic_small', 'small', 'medium', 'large', 'extra_large') NOT NULL");

    }
}
