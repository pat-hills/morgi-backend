<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewLeadersEmails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Email::create(['type' => 'LEADER_DID_NOT_PAID_FIRST_24H', 'sendgrid_id' => 'd-8b7d4acd0adf46fd86a3d5c031c94fee']);
        \App\Models\Email::create(['type' => 'LEADER_DID_NOT_PAID_AFTER_FIRST_EMAIL', 'sendgrid_id' => 'd-066af50b77844d2abf6ca661b90a5614']);
        \App\Models\Email::create(['type' => 'LEADER_DID_NOT_READ_MESSAGES', 'sendgrid_id' => 'd-ef182b4df0c64a8482eef8706fb94db5']);

        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->timestamp('opened_at')->nullable()->after('sendgrid_message_id');
            $table->timestamp('clicked_at')->nullable()->after('sendgrid_message_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->dropColumn('opened_at');
            $table->dropColumn('clicked_at');
        });
    }
}
