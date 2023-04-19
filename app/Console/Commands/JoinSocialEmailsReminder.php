<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserEmailSent;
use App\Services\Mailer\Mailer;
use App\Utils\EmailBlacklist\EmailBlacklistUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class JoinSocialEmailsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to users a join social email reminder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*
         * This cronjob is temp disabled, Ron requested this on 2022-12-14 00.28 via Whatsapp
         */
        return 0;

        $users = User::query()
            ->whereIn('type', ['rookie', 'leader'])
            ->where('created_at', '<=', Carbon::now()->subHours(32))
            ->where('active', true)
            ->where('clicked_facebook_link', false)
            ->where('clicked_telegram_link', false)
            ->get();

        foreach ($users as $user) {

            $already_sent = UserEmailSent::query()
                ->where('user_id', $user->id)
                ->where('type', 'SOCIAL_REMINDER')
                ->exists();

            if($already_sent){
                continue;
            }

            try {
                Mailer::create($user, $user->email)->setMisc()->setTemplate('SOCIAL_REMINDER')->sendAndCreateUserEmailSentRow();
                $this->info("Email sent to {$user->email}.");
            } catch (Exception $e) {
                $this->error("Unable to send email to {$user->email}. Error => " . $e->getMessage());
            }
        }

        return 0;
    }
}
