<?php

namespace App\Console\Commands;

use App\Mixpanel\Events\EventConnectTelegramSuccess;
use App\Mixpanel\Events\EventDisconnectTelegramSuccess;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserTelegramMessageSent;
use App\Telegram\TelegramUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UnsubscribeTelegramAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:unsubscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unsubscribe inactive telegram accounts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::query()->select('users.id', 'users.telegram_chat_id', 'rookies.first_name', 'users.username')
            ->join('rookies', 'users.id', '=', 'rookies.id')
            ->whereNotNull('users.joined_telegram_bot_at')
            ->where('users.type', 'rookie')
            ->groupBy('users.id')
            ->get();

        $this->info("Fetching " . count($users) . " Users");
        $this->info("");

        foreach ($users as $user){

            if(self::receivedSameMessages($user->id) && !self::receivedGiftInTheLastMonth($user->id)){

                $this->info("Unsubscribed {$user->id}");

                TelegramUtils::sendTelegramNotifications($user->telegram_chat_id, 'force_disconnect', ['rookie_first_name' => $user->first_name], $user->id);

                $user->unsubscribeFromTelegram();
            }
        }

        return 0;
    }

    private static function receivedGiftInTheLastMonth($user_id){

        return Transaction::query()
            ->where('rookie_id', $user_id)
            ->whereIn('type', ['chat','gift'])
            ->whereDate('created_at', Carbon::now()->subMonth())
            ->exists();
    }

    private static function receivedSameMessages($user_id){

        $telegram_unread_messages = UserTelegramMessageSent::query()
            ->where('type', 'unread_messages_reminder')
            ->where('user_id', $user_id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        if($telegram_unread_messages->count()<5){
            return false;
        }

        $value = $telegram_unread_messages->first()->unread_messages;

        foreach ($telegram_unread_messages as $message){
            if($message->unread_messages!==$value){
                return false;
            }
        }

        return true;
    }
}
