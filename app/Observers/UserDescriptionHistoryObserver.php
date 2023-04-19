<?php

namespace App\Observers;

use App\Models\UserDescriptionHistory;
use App\Utils\NotificationUtils;

class UserDescriptionHistoryObserver
{
    /**
     * Handle the UserDescriptionHistory "created" event.
     *
     * @param  \App\Models\UserDescriptionHistory  $userDescriptionHistory
     * @return void
     */
    public function created(UserDescriptionHistory $userDescriptionHistory)
    {
        UserDescriptionHistory::where('user_id', $userDescriptionHistory->user_id)
            ->where('id', '!=', $userDescriptionHistory->id)
            ->where('status', 'to_check')
            ->update(['status' => 'no_action']);
    }
}
