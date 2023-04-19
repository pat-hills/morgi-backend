<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixNotificationsTypeBlock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NotificationType::query()->where('type', 'rookie_blocked_leader')->update(['title' => 'A Rookie Has Blocked You!', 'content' => 'We are sorry to see that <ref_username> has decided to stop receiving mentorship from you. We have refunded you for your last gift to <ref_username>. Please allow 7 days for the funds to be refunded to your account. Any future Morgi gifts to this Rookie are canceled, too.']);
        NotificationType::query()->where('type', 'leader_blocked_rookie')->update(['title' => 'A Morgi Friend has blocked you!', 'content' => 'We are sorry to see that <ref_username> has decided to end their connection with you. Your path to greatness lies with other Friends!']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
