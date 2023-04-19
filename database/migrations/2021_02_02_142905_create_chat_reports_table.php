<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reporter_id');
            $table->bigInteger('reported_user_id');
            $table->string('message_id');
            $table->text('message_content');
            $table->bigInteger('chat_report_category_id');
            $table->text('report_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_reports');
    }
}
