<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestStatusColumnToPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->enum('request_status', ['pending', 'approved', 'declined', 'fully_funded', 'funding_progress'])->default('pending')->after('type');
        });
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
                $table->dropColumn('request_status');
            });
        }

    }
}
