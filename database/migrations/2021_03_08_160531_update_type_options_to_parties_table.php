<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateTypeOptionsToPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE parties CHANGE COLUMN type type ENUM('party', 'parade', 'photos_check', 'funding') NOT NULL");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasTable('parties')){
            Schema::table('parties', function (Blueprint $table) {
                DB::statement("ALTER TABLE parties CHANGE COLUMN type type ENUM('party', 'parade') NOT NULL");
            });
        }
    }
}
