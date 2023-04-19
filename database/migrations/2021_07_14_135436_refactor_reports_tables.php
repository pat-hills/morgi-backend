<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorReportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('chat_reports');
        Schema::dropIfExists('chat_reports_categories');

        Schema::table('complaints', function (Blueprint $table) {
            $table->text('notes')->nullable(true)->after('content');
            $table->bigInteger('message_id')->nullable(true)->after('type_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn('notes');
            $table->dropColumn('message_id');
        });
    }
}
