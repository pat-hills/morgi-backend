<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateColumnToPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->date('date_at')->nullable()->after('type');
            $table->time('hour_at')->nullable()->after('date_at');
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
                $table->dropColumn('date_at');
                $table->dropColumn('hour_at');
            });
        }
    }
}
