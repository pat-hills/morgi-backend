<?php

namespace App\Console\Commands;

use App\Models\ChannelReadTimetoken;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Models\User;
use App\Services\Chat\Utils;
use App\Telegram\TelegramUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TelegramUnreadMessagesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:unread_messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to rookies with attached telegram account notifications about unread messages';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /*
         * Actually we send that notifications only to rookies
         */
        $users = User::query()->select('users.id', 'users.telegram_chat_id', 'rookies.first_name', 'users.username', 'countries.timezone')
            ->join('rookies', 'users.id', '=', 'rookies.id')
            ->join('countries', 'countries.id', '=', 'rookies.country_id')
            ->whereNotNull('users.joined_telegram_bot_at')
            ->where('users.type', 'rookie')
            ->groupBy('users.id')
            ->get();

        $this->info("Fetching " . count($users) . " Users");
        $this->info("");

        foreach ($users as $user){

            /*
             * Send notification only at 6pm
             */
            $time = Carbon::now()->timezone($user->timezone)->hour;
            if($time !== 18){
                $this->info("For user $user->id the hour is $time");
                continue;
            }

            $unread_messages_count = 0;

            $pubnub_channels = PubnubChannel::query()
                ->where("rookie_id", $user->id)
                ->whereNull('user_block_id')
                ->get();

            foreach ($pubnub_channels as $pubnub_channel){

                /*
                 * Count only of active channels
                 */
                if(!$pubnub_channel->active){
                    continue;
                }

                $unread_messages = $pubnub_channel->getUnreadMessagesCount($user->id);
                if(isset($unread_messages)){
                    $this->info("User {$user->id} has $unread_messages unread messages in channel $pubnub_channel->id ($pubnub_channel->name)");
                    $unread_messages_count += $unread_messages;
                }
            }

            /*
             * Lets send Telegram's notification if there are unread messages
             */
            if($unread_messages_count > 0){
                TelegramUtils::sendTelegramNotifications($user->telegram_chat_id, 'unread_messages_reminder', [
                    'rookie_first_name' => $user->first_name ?? $user->username, 'unread_messages' => $unread_messages_count
                ], $user->id);
            }

            $this->info("User {$user->id} has $unread_messages_count unread messages!");
        }

        $this->info("Telegram's notifications sent!");
        return 0;
    }
}
