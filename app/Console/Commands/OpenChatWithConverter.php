<?php

namespace App\Console\Commands;

use App\Mixpanel\Events\EventConnectionOpenedByLuckyMatch;
use App\Models\ChannelReadTimetoken;
use App\Models\ConverterMessage;
use App\Models\PubnubChannel;
use App\Models\PubnubMessage;
use App\Orazio\OrazioHandler;
use App\Services\Chat\PubNub;
use App\Services\Mailer\Mailer;
use App\Utils\NotificationUtils;
use Carbon\Carbon;
use App\Models\User;
use App\Services\Chat\Chat;
use Illuminate\Console\Command;

class OpenChatWithConverter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open_chat:converter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Let you open chat with converter';

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
        $last_5_minutes = Carbon::now()->subMinutes(5);
        $leaders = User::query()->select("users.*")
            ->join('leaders', 'leaders.id', '=', 'users.id')
            ->where('leaders.has_converter_chat', false)
            ->where('users.active', true)
            ->where('users.type', 'leader')
            ->where('users.last_activity_at', '<', $last_5_minutes)
            ->groupBy('users.id')
            ->orderByDesc('users.last_activity_at')
            ->limit(50)
            ->inRandomOrder()
            ->get();

        if($leaders->count()===0){
            $this->info("No leaders to fetch");
            return 0;
        }

        $this->info("Leader fetched: " . $leaders->count());

        foreach($leaders as $leader){

            $this->info("Opening for leader: $leader->id");

            $rookie = User::selectRaw("users.*, rookies.first_name as first_name")
                ->join('rookies', 'rookies.id', '=', 'users.id')
                ->where('users.type', 'rookie')
                ->where('rookies.is_converter', true)
                ->inRandomOrder()
                ->first();

            if(!isset($rookie)){
                $this->error("Unable to retrieve an active converter for leader {$leader->id}");
                continue;
            }

            $channel_already_exists = PubnubChannel::query()->where('leader_id', $leader->id)
                ->where('rookie_id', $rookie->id)
                ->exists();
            if($channel_already_exists){
                $leader->update(['has_converter_chat' => true]);
                $this->error("Leader {$leader->id} and Rookie {$rookie->id} already have a channel opened");
                continue;
            }

            try {
                $channel = Chat::config($leader->id)->startDirectChat(
                    $leader,
                    $rookie,
                    null,
                    null,
                    false,
                    true
                );
            } catch (\Exception $exception) {
                $this->error("Unable to open channel between {$leader->id} and {$rookie->id}: {$exception->getMessage()}");
            }

            if(!isset($channel)){
                $this->error("Unable to open channel between {$leader->id} and {$rookie->id}: Unable to create channel");
                continue;
            }

            try {
                NotificationUtils::sendNotification($leader->id, "converter_first_message", now(),
                    ['ref_user_id' => $rookie->id]
                );

                Mailer::create($leader)->setMisc([
                        'rookie_avatar' => $rookie->getOwnAvatar()->url ?? null,
                        'rookie_name' => $rookie->first_name,
                        'channel_link' => env('FRONTEND_URL') . "/message-center/{$channel->name}"
                    ])
                    ->setTemplate('CONVERTER_FIRST_MESSAGE')
                    ->sendAndCreateUserEmailSentRow(now()->addMinutes(5)->timestamp);
            }catch (\Exception $exception){
                $this->error("Unable to send notification between {$leader->id} and {$rookie->id}: {$exception->getMessage()}");
            }

            $converter_message = ConverterMessage::query()->where('rookie_id', $rookie->id)->first();
            if(isset($converter_message)){
                try {
                    $this->sendChatBroadcast($channel, $converter_message->message);
                    PubnubMessage::query()->create([
                        'type' => 'message',
                        'sender_id' => $channel->rookie_id,
                        'receiver_id' => $channel->leader_id,
                        'channel_id' => $channel->id,
                        'sent_at' => now()
                    ]);
                    $channel->update([
                        'rookie_first_message_at' => now()
                    ]);
                }catch (\Exception $exception){
                    $this->error("Unable to send first message in channel {$channel->id}: {$exception->getMessage()}");
                }
            }

            $leader->update(['has_converter_chat' => true]);

            try {
                OrazioHandler::freshSeen($leader->id, 'Connected with converter by cronjob', true);
            }catch (\Exception $exception){
                $this->error("Unable to recalculate orazio for {$leader->id}: {$exception->getMessage()}");
            }

            try {
                EventConnectionOpenedByLuckyMatch::config($leader->id, $rookie->id);
            }catch (\Exception $exception){
            }
        }

        $this->info("Channels opened!");
        return 0;
    }

    private function sendChatBroadcast(PubnubChannel $channel, string $message): void
    {
        $payload = [
            'type' => 'text',
            'message' => $message,
            'user_id' => $channel->rookie_id
        ];

        try {
            PubNub::config($channel->rookie_id)->broadcast($channel->name, $payload);
            ChannelReadTimetoken::updateOrCreate([
                'user_id' => $channel->rookie_id,
                'channel_id' => $channel->id
            ], [
                'timetoken' => \App\Services\Chat\Utils::getTimetokenFromTimestamp(now()->addSecond()->timestamp)
            ]);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }
}
