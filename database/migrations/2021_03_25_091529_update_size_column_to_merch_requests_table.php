<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSizeColumnToMerchRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE merch_requests CHANGE COLUMN size size ENUM('classic_small', 'small', 'medium', 'large', 'extra_large') DEFAULT NULL");
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
            DB::statement("ALTER TABLE merch_requests CHANGE COLUMN size size ENUM('classic_small', 'small', 'medium', 'large', 'extra_large') NOT NULL");

        });
    }
}
