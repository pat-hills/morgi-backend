<?php

namespace App\Enums;

class ActionTrackingEnum
{
    const CLICKED_PROFILE = 'clicked_profile';
    const SAW_VIDEO = 'saw_video';
    const SHARED_PROFILE = 'shared_profile';
    const TIME_IN_ROOKIE_PROFILE_IN_SECONDS = 'time_in_rookie_profile_in_seconds';

    const ACTIONS = [
        self::CLICKED_PROFILE,
        self::SAW_VIDEO,
        self::SHARED_PROFILE,
        self::TIME_IN_ROOKIE_PROFILE_IN_SECONDS
    ];
}
