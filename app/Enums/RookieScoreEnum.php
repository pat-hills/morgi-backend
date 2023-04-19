<?php

namespace App\Enums;

class RookieScoreEnum
{
    const LOGINS_STREAK = 'logins_streak';
    const PHOTOS = 'photos';
    const VIDEOS = 'videos';
    const HAS_DESCRIPTION = 'has_description';
    const JOINED_TELEGRAM_BOT = 'joined_telegram_bot';
    const FIRST_MICROMORGI_GIFT_LEADERS = 'first_micromorgi_gift_leaders';
    const LEADERS_REFERRED = 'leaders_referred';
    const LEADERS_FIRST_SUBSCRIPTION = 'leaders_first_subscription';

    const ACTION_BASED_ATTRIBUTES = [
        self::LOGINS_STREAK,
        self::PHOTOS,
        self::VIDEOS,
        self::HAS_DESCRIPTION,
        self::JOINED_TELEGRAM_BOT,
        self::FIRST_MICROMORGI_GIFT_LEADERS,
        self::LEADERS_REFERRED,
        self::LEADERS_FIRST_SUBSCRIPTION
    ];

    const ACTIVE_SUBSCRIPTIONS = 'active_subscriptions';
    const LEADERS_SENDING_MICROMORGI_LAST_SEVEN_DAYS = 'leaders_sending_micromorgi_last_seven_days';
    const AVG_SUBSCRIPTIONS_PERIOD = 'avg_subscriptions_period';
    const AVG_FIRST_RESPONSE_TIME_SECONDS = 'avg_first_response_time_seconds';

    const PERFORMANCE_BASED_ATTRIBUTES = [
        self::ACTIVE_SUBSCRIPTIONS,
        self::LEADERS_SENDING_MICROMORGI_LAST_SEVEN_DAYS,
        self::AVG_SUBSCRIPTIONS_PERIOD,
        self::AVG_FIRST_RESPONSE_TIME_SECONDS
    ];

    const HUNGRY_ROOKIES = 'hungry_rookies';
    const LEADERS_RETAINING_ROOKIE = 'leaders_retaining_rookie';
    const AVG_GIFTS_AMOUNTS = 'avg_gifts_amounts';
    const CONVERTERS_SUBSCRIPTIONS = 'converters_subscriptions';
    const TIME_TO_SUBSCRIBE = 'time_to_subscribe';

    const BACKEND_PERFORMANCE_BASED_ATTRIBUTES = [
        self::HUNGRY_ROOKIES,
        self::LEADERS_RETAINING_ROOKIE,
        self::AVG_GIFTS_AMOUNTS,
        self::CONVERTERS_SUBSCRIPTIONS,
        self::TIME_TO_SUBSCRIBE
    ];

    const ATTRIBUTES_MAX_SCORE = [
        'logins_streak' => 10,
        'photos' => 20,
        'videos' => 10,
        'has_description' => 10,
        'joined_telegram_bot' => 40,
        'first_micromorgi_gift_leaders' => 30,
        'leaders_referred' => 30,
        'active_subscriptions' => 40,
        'leaders_sending_micromorgi_last_seven_days' => 30,
        'avg_subscriptions_period' => 40,
        'avg_first_response_time_seconds' => 40,
        'avg_gifts_amounts' => 40,
        'leaders_retaining_rookie' => 40,
        'hungry_rookies' => 40,
        'converters_subscriptions' => 50,
        'time_to_subscribe' => 50,
        'leaders_first_subscription' => 1000000000
    ];

    const ATTRIBUTES_REVERSE_SCORE = [
        self::AVG_SUBSCRIPTIONS_PERIOD,
        self::AVG_FIRST_RESPONSE_TIME_SECONDS,
        self::TIME_TO_SUBSCRIBE
    ];

    const SCORE_SELECT_QUERY = "rookies_score.logins_streak
        + rookies_score.photos + rookies_score.videos
        + rookies_score.has_description + rookies_score.joined_telegram_bot
        + rookies_score.first_micromorgi_gift_leaders
        + rookies_score.leaders_referred + rookies_score.active_subscriptions
        + rookies_score.leaders_sending_micromorgi_last_seven_days
        + rookies_score.avg_subscriptions_period + rookies_score.avg_first_response_time_seconds";

    const BEST_SCORE_SELECT_QUERY = "rookies_score.logins_streak
        + rookies_score.photos + rookies_score.videos
        + rookies_score.has_description + rookies_score.joined_telegram_bot
        + rookies_score.first_micromorgi_gift_leaders
        + rookies_score.leaders_referred + rookies_score.active_subscriptions
        + rookies_score.leaders_sending_micromorgi_last_seven_days
        + rookies_score.avg_subscriptions_period + rookies_score.avg_first_response_time_seconds
        + rookies_score.avg_gifts_amounts + rookies_score.leaders_retaining_rookie
        + rookies_score.hungry_rookies + rookies_score.leaders_first_subscription
        + rookies_score.converters_subscriptions + rookies_score.time_to_subscribe";
}
