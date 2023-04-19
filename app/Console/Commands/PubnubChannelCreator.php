<?php

namespace App\Console\Commands;

use App\Enums\JobHistoryEnum;
use App\Enums\PubnubGroupEnum;
use App\Models\JobHistory;
use App\Models\PubnubChannel;
use App\Models\User;
use App\Services\Chat\PubNub;
use Illuminate\Console\Command;

class PubnubChannelCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubnub:channels-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create failed channels on pubnub';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $job_history = JobHistory::query()->create(['type' => JobHistoryEnum::PUBNUB_CHANNEL_CREATOR, 'start_at' => now(), 'end_at' => now()]);

        $channels = PubnubChannel::where('created', false)->get();
        $direct_category = PubnubGroupEnum::DIRECT_CATEGORY;
        $this->alert("Fetched " . $channels->count() . " Channels!");

        foreach ($channels as $channel){

            $leader = User::find($channel->leader_id);
            $rookie = User::find($channel->rookie_id);

            if(!isset($leader) || !isset($rookie)){
                $this->error("Leader or Rookie not found in channel: " . $channel->name);
            }

            $leader_direct_group = $leader->latestOrCreateGroupByCategory($direct_category);
            $rookie_direct_group = $rookie->latestOrCreateGroupByCategory($direct_category);

            if(!isset($leader_direct_group) || !isset($rookie_direct_group)){
                $this->error("Leader or Rookie Group not found in channel: " . $channel->name);
            }

            $pubnub = PubNub::config($leader->id);
            $pubnub->addChannelsToChannelsGroups([$channel->name], [$leader_direct_group->name, $rookie_direct_group->name]);
            $pubnub->setChannelMembers($channel->name, [(string)$leader->id, (string)$rookie->id]);

            $channel->update(['created' => true]);

            $this->info("Created " . $channel->name . " Channel!");
        }

        $job_history->update(['completed' => true, 'completed_at' => now()]);

        return 0;
    }
}
