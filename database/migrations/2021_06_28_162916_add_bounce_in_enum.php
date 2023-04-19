<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBounceInEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE sendgrid_emails_checks CHANGE COLUMN type type ENUM('invalid_email','block','spam_report', 'bounce')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE sendgrid_emails_checks CHANGE COLUMN type type ENUM('invalid_email','block','spam_report')");
    }
}
