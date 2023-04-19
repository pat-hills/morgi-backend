<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserEmailSent;
use App\Services\Mailer\Mailer;
use Illuminate\Console\Command;

class LeaderDidNotPaidEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaders:not-paying-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to leaders were not paid rookies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*
         * Retrieve alla users that nevere paid something
         */
        $users = User::query()
            ->where('type', 'leader')
            ->where('active', true)
            ->where('total_subscriptions_count', 0)
            ->get();

        $this->alert("Fetched {$users->count()} Users!");

        foreach ($users as $user){

            $user_email_sent = UserEmailSent::query()->where('user_id', $user->id)
                ->where('type', 'LEADER_DID_NOT_PAID_FIRST_24H')
                ->first();

            $second_user_email_sent = UserEmailSent::query()->where('user_id', $user->id)
                ->where('type', 'LEADER_DID_NOT_PAID_FIRST_24H')
                ->first();

            /*
             * Send LEADER_DID_NOT_PAID_FIRST_24H email after 24h of leader's subscription without paying rookies
             */
            if(!isset($user_email_sent) && strtotime($user->created_at) > now()->subDay()->timestamp){

                $this->info("Sending LEADER_DID_NOT_PAID_FIRST_24H to {$user->id}");
                try {
                    Mailer::create($user)->setMisc()->setTemplate('LEADER_DID_NOT_PAID_FIRST_24H')->sendAndCreateUserEmailSentRow();
                }catch (\Exception $exception){
                }
                continue;
            }

            /*
             * Send LEADER_DID_NOT_PAID_AFTER_FIRST_EMAIL email after 48h of first email open action
             */
            if(!isset($second_user_email_sent) && isset($user_email_sent) && isset($user_email_sent->opened_at) &&
                strtotime($user_email_sent->opened_at) < now()->subDays(2)->timestamp){

                $this->info("Sending LEADER_DID_NOT_PAID_AFTER_FIRST_EMAIL to {$user->id}");
                try {
                    Mailer::create($user)->setMisc()->setTemplate('LEADER_DID_NOT_PAID_AFTER_FIRST_EMAIL')->sendAndCreateUserEmailSentRow();
                }catch (\Exception $exception){
                }
            }
        }

        return 0;
    }
}
