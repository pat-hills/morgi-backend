<?php

namespace App\Enums;

class ContentEditorEnum
{
    const INSPIRATION = 'inspiration';
    const NEWS_UPDATE = 'news_update';
    const MORGI_S_INSPIRATION = "morgi's inspiration";
    const DAILY_NEWS = 'daily news';

    const TYPES = [
        self::INSPIRATION => self::MORGI_S_INSPIRATION,
        self::NEWS_UPDATE => self::DAILY_NEWS
    ];
}
