<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSendgridEmailsChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sendgrid_emails_checks', function (Blueprint $table) {
            $table->integer('emails_count')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sendgrid_emails_checks', function (Blueprint $table) {
            $table->dropColumn('emails_count');
        });
    }
}
