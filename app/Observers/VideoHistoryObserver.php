<?php

namespace App\Observers;

use App\Models\VideoHistory;
use App\Utils\NotificationUtils;

class VideoHistoryObserver
{
    /**
     * Handle the VideoHistory "created" event.
     *
     * @param  \App\Models\VideoHistory  $videoHistory
     * @return void
     */
    public function created(VideoHistory $videoHistory)
    {
        VideoHistory::where('user_id', $videoHistory->user_id)
            ->where('id', '!=', $videoHistory->id)
            ->where('status', 'to_check')
            ->update(['status' => 'no_action']);
    }
}
