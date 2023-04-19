<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mails = [
            ['type' => 'MERCH_SHIPPED', 'sendgrid_id' => 'd-6cedfcb4af8a4edc990866306fbd18b6'],
            ['type' => 'ROOKIE_PAYMENT_REJECTED', 'sendgrid_id' => 'd-17fa405f455941d3b5a9ab164acc7753']
        ];

        \App\Models\Email::insert(
            $mails
        );

        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
