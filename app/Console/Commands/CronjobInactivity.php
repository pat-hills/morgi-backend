<?php

namespace App\Console\Commands;

use App\Models\PubnubMessage;
use App\Models\Rookie;
use App\Models\User;
use App\Telegram\TelegramUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CronjobInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:inactive-leaders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send messages by telegram bot to inactive leaders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->alert('Started sending disconnect message');

        /*
         * Disconnect after 48h of inactivity
         */
        $leaders_to_disconnect = User::query()->where('type', 'leader')
            ->whereNotNull('joined_telegram_bot_at')
            ->where('can_receive_telegram_message', true)
            ->where('last_activity_at', '<=', Carbon::now()->subDays(2));

        $leaders_to_disconnect_users = $leaders_to_disconnect->get();
        $this->info("Disconnecting " . $leaders_to_disconnect_users->count() . " leaders");
        foreach ($leaders_to_disconnect_users as $leader_to_disconnect) {

            $data = [
                'message_center' => env('FRONTEND_URL') . '/message-center',
                'first_name' => $leader_to_disconnect->username
            ];

            TelegramUtils::sendTelegramNotifications($leader_to_disconnect->telegram_chat_id, 'inactive_leader', $data, $leader_to_disconnect->id);
            $this->info('Message sent to '. $leader_to_disconnect->username);
        }

        $leaders_to_disconnect->update(['can_receive_telegram_message' => false]);

        $this->alert('Started sending inactivity ping');

        /*
         * Send ping after 24h of inactivity
         */
        $leaders_to_ping = User::query()->where('type', 'leader')
            ->whereNotNull('joined_telegram_bot_at')
            ->where('can_receive_telegram_message', true)
            ->where('last_activity_at', '<=', Carbon::now()->subDay())
            ->get();

        $this->info("Ping to " . $leaders_to_ping->count() . " leaders");

        foreach ($leaders_to_ping as $leader_to_ping) {

            $messages = PubnubMessage::query()
                ->where('receiver_id', $leader_to_ping->id)
                ->where('sent_at', '>=', $leader_to_ping->last_activity_at)
                ->get();

            $messages_count = $messages->count();
            if($messages_count <= 0){
                continue;
            }

            $rookies_names = Rookie::query()->select('first_name')
                ->whereIn('id', $messages->pluck('sender_id'))
                ->get()
                ->pluck('first_name')
                ->toArray();

            $data = [
                'message_center' => env('FRONTEND_URL') . '/message-center',
                'first_name' => $leader_to_ping->username,
                'rookies_names' => $this->stringifyRookiesNames($rookies_names),
                'messages_count' => $messages_count
            ];

            TelegramUtils::sendTelegramNotifications($leader_to_ping->telegram_chat_id, 'inactive_leader_ping', $data, $leader_to_ping->id);
            $this->info('Message sent to '. $leader_to_ping->username);
        }
        return 0;
    }

    private function stringifyRookiesNames(array $rookies_names): string
    {
        $string = '';
        $latest_key = count($rookies_names) - 1;
        foreach ($rookies_names as $key => $name){

            if($latest_key === 0){
                $string .= $name;
                continue;
            }

            if($latest_key === $key){
                $string .= " and $name";
                continue;
            }

            $string .= ($key === 0 ) ? $name : ", $name";
        }

        return $string;
    }
}
