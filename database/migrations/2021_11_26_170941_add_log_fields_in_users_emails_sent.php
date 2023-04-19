<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogFieldsInUsersEmailsSent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->text('errors')->nullable(true)->after('user_id');
            $table->string('sendgrid_message_id')->nullable(true)->after('user_id');
            $table->boolean('sent')->default(false)->after('user_id');
        });

        $emails = \App\Models\UserEmailSent::all();

        foreach ($emails as $email){
            $email->update(['sent' => $email->type!=='SOCIAL_REMINDER']);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_emails_sent', function (Blueprint $table) {
            $table->dropColumn('errors');
            $table->dropColumn('sendgrid_message_id');
            $table->dropColumn('sent');
        });
    }
}
