<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasConverterChatFieldToLeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->boolean('has_converter_chat')->default(false)->after('first_rookie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropColumn('has_converter_chat');
        });
    }
}
