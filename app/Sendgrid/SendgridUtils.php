<?php


namespace App\Sendgrid;


use App\Enums\SendGridEmailCheckEnum;
use App\Models\SendGridEmailCheck;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;

class SendgridUtils
{
    public static function handleSpamReports()
    {
        $spam_report = SendGridEmailCheck::where('type', SendGridEmailCheckEnum::SPAM_REPORT)->latest()->first();

        /*
         * Default time is 2021/1/1 00:00:00
         */
        $start_time = ($spam_report) ? strtotime($spam_report->created_at) : 1609455600;
        $end_time = strtotime(now());

        $sendgrid = new SendgridAPI();
        $response = $sendgrid->spamReports($start_time, $end_time);

        if ($response['status'] === 'error') {
            return ['status' => 'error'];
        }

        $datas = $response['data'];

        foreach ($datas as $data) {
            EmailBlacklistUtils::set($data->email)->firstOrCreate();
        }

        SendGridEmailCheck::create(['type' => SendGridEmailCheckEnum::SPAM_REPORT, 'emails_count' => count($datas)]);

        return ['status' => true];
    }

    public static function handleBlockReports()
    {
        $spam_report = SendGridEmailCheck::where('type', SendGridEmailCheckEnum::BLOCK)->latest()->first();

        /*
         * Default time is 2021/1/1 00:00:00
         */
        $start_time = ($spam_report) ? strtotime($spam_report->created_at) : 1609455600;
        $end_time = strtotime(now());

        $sendgrid = new SendgridAPI();
        $response = $sendgrid->blocks($start_time, $end_time);

        if ($response['status'] === 'error') {
            return ['status' => 'error'];
        }

        $datas = $response['data'];

        foreach ($datas as $data) {
            EmailBlacklistUtils::set($data->email)->firstOrCreate();
        }

        SendGridEmailCheck::create(['type' => SendGridEmailCheckEnum::BLOCK, 'emails_count' => count($datas)]);

        return ['status' => true];
    }

    public static function handleInvalidEmailsReports()
    {
        $spam_report = SendGridEmailCheck::where('type', SendGridEmailCheckEnum::INVALID_EMAIL)->latest()->first();

        /*
         * Default time is 2021/1/1 00:00:00
         */
        $start_time = ($spam_report) ? strtotime($spam_report->created_at) : 1609455600;
        $end_time = strtotime(now());

        $sendgrid = new SendgridAPI();
        $response = $sendgrid->invalidEmails($start_time, $end_time);

        if ($response['status'] === 'error') {
            return ['status' => 'error'];
        }

        $datas = $response['data'];

        foreach ($datas as $data) {
            EmailBlacklistUtils::set($data->email)->firstOrCreate();
        }

        SendGridEmailCheck::create(['type' => SendGridEmailCheckEnum::INVALID_EMAIL, 'emails_count' => count($datas)]);

        return ['status' => true];
    }

    public static function handleBouncesReports()
    {
        $spam_report = SendGridEmailCheck::where('type', SendGridEmailCheckEnum::BOUNCE)->latest()->first();

        /*
         * Default time is 2021/1/1 00:00:00
         */
        $start_time = ($spam_report) ? strtotime($spam_report->created_at) : 1609455600;
        $end_time = strtotime(now());

        $sendgrid = new SendgridAPI();
        $response = $sendgrid->bounces($start_time, $end_time);

        if ($response['status'] === 'error') {
            return ['status' => 'error'];
        }

        $datas = $response['data'];

        foreach ($datas as $data) {
            EmailBlacklistUtils::set($data->email)->firstOrCreate();
        }

        SendGridEmailCheck::create(['type' => SendGridEmailCheckEnum::BOUNCE, 'emails_count' => count($datas)]);

        return ['status' => true];
    }

}
