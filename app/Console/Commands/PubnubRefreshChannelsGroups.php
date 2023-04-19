<?php

namespace App\Console\Commands;

use App\Models\PubnubChannel;
use App\Models\PubnubGroup;
use App\Models\User;
use App\Services\Chat\Chat;
use App\Services\Chat\PubNub;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PubnubRefreshChannelsGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubnub:channels-groups:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->alert('Script started at ' . Carbon::now()->toDateTimeString());

        $channels = PubnubChannel::query()
            ->selectRaw('pubnub_channels.*')
            ->leftJoin('pubnub_groups_channels', 'pubnub_groups_channels.channel_id', '=', 'pubnub_channels.id')
            ->whereNull('pubnub_groups_channels.id')
            ->groupBy('pubnub_channels.id')
            ->get();

        $this->info('Channels fetched:' . $channels->count());

        $rookies_ids = $channels->pluck('rookie_id')->toArray();
        $leaders_ids = $channels->pluck('leader_id')->toArray();
        $users_ids = array_merge($rookies_ids, $leaders_ids);

        $users = User::query()->findMany($users_ids);
        $this->info('Users fetched:' . $users->count());

        foreach ($channels as $channel){
            $leader_user = $users->where('id', $channel->leader_id)->first();
            $rookie_user = $users->where('id', $channel->rookie_id)->first();

            if(!isset($leader_user)){
                $this->error("Unable to retrieve leader for channel {$channel->id}");
                continue;
            }

            if(!isset($rookie_user)){
                $this->error("Unable to retrieve rookie for channel {$channel->id}");
                continue;
            }

            Chat::config($leader_user->id)->addChannelToChannelsGroups($channel, $leader_user, $rookie_user);
            $this->info("Refreshed channel {$channel->id}");
        }

        $this->alert('Finished at ' . Carbon::now()->toDateTimeString());
        return 0;
    }
}
