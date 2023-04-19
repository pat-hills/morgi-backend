<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Mailer\Mailer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RookieVerifyEmailReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send profile improvement emails';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::now()->subHours(32)->toDateTimeString();
        $users = User::query()->select('users.*')
            ->leftJoin('users_emails_sent', function ($q){
                return $q->on('users_emails_sent.user_id', '=', 'users.id')
                    ->where('users_emails_sent.type', 'VERIFY_EMAIL_REMINDER');
            })
            ->groupBy('users.id')
            ->where('users.type', 'rookie')
            ->whereIn('users.status', ['pending', 'new'])
            ->whereNull('users.email_verified_at')
            ->where('users.created_at', '<=', $date)
            ->havingRaw("COUNT(users_emails_sent.id)=0")
            ->get();

        foreach ($users as $user){

            try {
                Mailer::create($user)->setMisc()->setTemplate('VERIFY_EMAIL_REMINDER')->sendAndCreateUserEmailSentRow();
            }catch (\Exception $exception){
                $this->error("Unable to send email to {$user->id}: {$exception->getMessage()}");
            }

            $this->info("Sending email to {$user->id}");
            sleep(0.2);
        }

        $this->info("");
        $this->info("Emails sent successfully!");
        return 0;
    }
}
